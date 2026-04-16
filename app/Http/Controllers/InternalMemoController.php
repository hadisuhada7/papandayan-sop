<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInternalMemoRequest;
use App\Http\Requests\UpdateInternalMemoRequest;
use App\Models\Company;
use App\Models\Category;
use App\Models\InternalMemo;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class InternalMemoController extends Controller
{
    use LogsAuditTrail;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Super Admin sees all internal memos
        if ($user->hasRole('super_admin')) {
            $internalMemos = InternalMemo::with(['categories', 'company'])->orderBy('id')->get();
        } else {
            // Admin/Viewer only see internal memos from their company
            $selectedCompanyId = session('selected_company_id');
            $internalMemos = InternalMemo::where('company_id', $selectedCompanyId)
                ->with(['categories', 'company'])
                ->orderBy('id')
                ->get();
        }
        
        return view('admin.internal-memos.index', compact('internalMemos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Super Admin sees all companies and categories
        if ($user->hasRole('super_admin')) {
            $companies = Company::all();
            $categories = Category::all();
        } else {
            // Admin/Viewer only see their company and categories
            $selectedCompanyId = session('selected_company_id');
            $company = Company::find($selectedCompanyId);
            $companies = $company ? collect([$company]) : collect([]);
            $categories = Category::where('company_id', $selectedCompanyId)->get();
        }
        
        return view('admin.internal-memos.create', compact('companies', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInternalMemoRequest $request)
    {
        try {
            // Closure-based transaction
            DB::transaction(function () use ($request) {
                $validated = $request->validated();
                $categoryIds = $validated['category_ids'] ?? [];
                unset($validated['category_ids']);

                // Handle no_expired_date checkbox
                if (!empty($validated['no_expired_date'])) {
                    $validated['expired_date'] = null;
                }
                unset($validated['no_expired_date']);

                // Remove documents data from validated
                $documentNames = $request->input('document_names', []);
                $documentFiles = $request->file('documents', []);
                
                $internalMemo = InternalMemo::create($validated);
                $internalMemo->categories()->sync($categoryIds);
                
                // Handle documents
                if (!empty($documentNames) && !empty($documentFiles)) {
                    foreach ($documentNames as $index => $name) {
                        if (isset($documentFiles[$index])) {
                            $file = $documentFiles[$index];
                            $originalName = $file->getClientOriginalName();
                            $attachmentPath = $file->storeAs('documents', $originalName, 'public');
                            
                            $internalMemo->documents()->create([
                                'name' => $name,
                                'attachment' => $attachmentPath,
                            ]);
                        }
                    }
                }
                
                // Log audit trail
                $this->logAuditTrail('Created Internal Memo', "Created Internal Memo: {$internalMemo->title}");
            });

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Internal Memo created successfully.',
                    'redirect' => route('admin.internal-memos.index', [], false),
                ]);
            }

            return redirect()->route('admin.internal-memos.index')->with('toast', ['type' => 'success', 'message' => 'Internal Memo created successfully.']);
        } catch (Throwable $e) {
            Log::error('Failed to store internal memo', [
                'error' => $e->getMessage(),
                'document_names_count' => count($request->input('document_names', [])),
                'document_files_count' => count($request->file('documents', [])),
                'user_id' => optional(auth()->user())->id,
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => config('app.debug')
                        ? $e->getMessage()
                        : 'Failed to save Internal Memo. Please check document attachments and try again.',
                ], 500);
            }

            return back()->withInput()->with('toast', [
                'type' => 'error',
                'message' => 'Failed to save Internal Memo. Please try again.',
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(InternalMemo $internalMemo)
    {
        $user = auth()->user();
        
        // For non-super admin users, verify access
        if (!$user->hasRole('super_admin')) {
            $selectedCompanyId = session('selected_company_id');
            
            // Verify internal memo belongs to their company
            if ($internalMemo->company_id !== $selectedCompanyId) {
                abort(403, 'Unauthorized access to this internal memo.');
            }
        }
        
        // Load relationships
        $internalMemo->load(['company', 'categories', 'documents']);
        
        return view('admin.internal-memos.detail', compact('internalMemo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InternalMemo $internalMemo)
    {
        $user = auth()->user();
        
        // Super Admin sees all companies and categories
        if ($user->hasRole('super_admin')) {
            $companies = Company::all();
            $categories = Category::all();
        } else {
            // Admin/Viewer only see their company and categories
            $selectedCompanyId = session('selected_company_id');
            
            // Verify internal memo belongs to their company
            if ($internalMemo->company_id !== $selectedCompanyId) {
                abort(403, 'Unauthorized access to this internal memo.');
            }
            
            $company = Company::find($selectedCompanyId);
            $companies = $company ? collect([$company]) : collect([]);
            $categories = Category::where('company_id', $selectedCompanyId)->get();
        }
        
        $internalMemo->load('categories');
        return view('admin.internal-memos.edit', compact('internalMemo', 'companies', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInternalMemoRequest $request, InternalMemo $internalMemo)
    {
        try {
        // Closure-based transaction
            DB::transaction(function () use ($request, $internalMemo) {
                $validated = $request->validated();
                $categoryIds = $validated['category_ids'] ?? [];
                unset($validated['category_ids']);

                // Handle no_expired_date checkbox
                if (!empty($validated['no_expired_date'])) {
                    $validated['expired_date'] = null;
                }
                unset($validated['no_expired_date']);

                // Remove documents data from validated
                $documentNames = $request->input('document_names', []);
                $documentFiles = $request->file('documents', []);
                $existingDocumentIds = $request->input('existing_document_ids', []);
                
                $internalMemo->update($validated);
                $internalMemo->categories()->sync($categoryIds);
                
                // Handle documents - delete documents not in the list
                if (!empty($existingDocumentIds)) {
                    $internalMemo->documents()->whereNotIn('id', $existingDocumentIds)->delete();
                } else {
                    // Delete all documents if none are submitted
                    $internalMemo->documents()->delete();
                }
                
                // Add new documents
                if (!empty($documentNames) && !empty($documentFiles)) {
                    foreach ($documentNames as $index => $name) {
                        if (isset($documentFiles[$index])) {
                            $file = $documentFiles[$index];
                            $originalName = $file->getClientOriginalName();
                            $attachmentPath = $file->storeAs('documents', $originalName, 'public');
                            
                            $internalMemo->documents()->create([
                                'name' => $name,
                                'attachment' => $attachmentPath,
                            ]);
                        }
                    }
                }
                
                // Log audit trail
                $this->logAuditTrail('Updated Internal Memo', "Updated Internal Memo: {$internalMemo->title}");
            });

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Internal Memo updated successfully.',
                    'redirect' => route('admin.internal-memos.index', [], false),
                ]);
            }

            return redirect()->route('admin.internal-memos.index')->with('toast', ['type' => 'success', 'message' => 'Internal Memo updated successfully.']);
        } catch (Throwable $e) {
            Log::error('Failed to update internal memo', [
                'internal_memo_id' => $internalMemo->id,
                'error' => $e->getMessage(),
                'document_names_count' => count($request->input('document_names', [])),
                'document_files_count' => count($request->file('documents', [])),
                'user_id' => optional(auth()->user())->id,
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => config('app.debug')
                        ? $e->getMessage()
                        : 'Failed to update Internal Memo. Please check document attachments and try again.',
                ], 500);
            }

            return back()->withInput()->with('toast', [
                'type' => 'error',
                'message' => 'Failed to update Internal Memo. Please try again.',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InternalMemo $internalMemo)
    {
        $internalMemoTitle = $internalMemo->title;
        
        // Closure-based transaction
        DB::transaction(function () use ($internalMemo, $internalMemoTitle) {
            $internalMemo->delete();
            
            // Log audit trail
            $this->logAuditTrail('Deleted Internal Memo', "Deleted Internal Memo: {$internalMemoTitle}");
        });

        return redirect()->route('admin.internal-memos.index')->with('toast', ['type' => 'success', 'message' => 'Internal Memo deleted successfully.']);
    }

    /**
     * Upload image from Summernote editor.
     */
    public function uploadSummernoteImage(Request $request)
    {
        $request->validate([
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Max 2MB
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('summernote-images', $filename, 'public');
            
            return response()->json([
                'url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['error' => 'No image uploaded'], 400);
    }
}
