<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStandardOperationalRequest;
use App\Http\Requests\UpdateStandardOperationalRequest;
use App\Models\Company;
use App\Models\Category;
use App\Models\StandardOperational;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class StandardOperationalController extends Controller
{
    use LogsAuditTrail;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Super Admin sees all standard operationals
        if ($user->hasRole('super_admin')) {
            $standardOperationals = StandardOperational::with(['categories', 'company'])->orderBy('id')->get();
        } else {
            // Admin/Viewer only see standard operationals from their company
            $selectedCompanyId = session('selected_company_id');
            $standardOperationals = StandardOperational::where('company_id', $selectedCompanyId)
                ->with(['categories', 'company'])
                ->orderBy('id')
                ->get();
        }
        
        return view('admin.standard-operationals.index', compact('standardOperationals'));
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
        
        return view('admin.standard-operationals.create', compact('companies', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStandardOperationalRequest $request)
    {
        try {
            // Closure-based transaction
            DB::transaction(function () use ($request) {
                $validated = $request->validated();
                $categoryIds = $validated['category_ids'] ?? [];
                unset($validated['category_ids']);

                // Remove documents data from validated
                $documentNames = $request->input('document_names', []);
                $documentTypes = $request->input('document_types', []);
                $documentFiles = $request->file('documents', []);
                
                $standardOperational = StandardOperational::create($validated);
                $standardOperational->categories()->sync($categoryIds);
                
                // Handle documents
                if (!empty($documentNames) && !empty($documentFiles)) {
                    foreach ($documentNames as $index => $name) {
                        if (isset($documentFiles[$index])) {
                            $file = $documentFiles[$index];
                            $originalName = $file->getClientOriginalName();
                            $attachmentPath = $file->storeAs('documents', $originalName, 'public');
                            
                            $standardOperational->documents()->create([
                                'name' => $name,
                                'type' => $documentTypes[$index] ?? null,
                                'attachment' => $attachmentPath,
                            ]);
                        }
                    }
                }
                
                // Log audit trail
                $this->logAuditTrail('Created Standard Operational', "Created SOP: {$standardOperational->title}");
            });

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Standard Operational created successfully.',
                    'redirect' => route('admin.standard-operationals.index', [], false),
                ]);
            }

            return redirect()->route('admin.standard-operationals.index')->with('toast', ['type' => 'success', 'message' => 'Standard Operational created successfully.']);
        } catch (Throwable $e) {
            Log::error('Failed to store standard operational', [
                'error' => $e->getMessage(),
                'document_names_count' => count($request->input('document_names', [])),
                'document_files_count' => count($request->file('documents', [])),
                'user_id' => optional(auth()->user())->id,
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => config('app.debug')
                        ? $e->getMessage()
                        : 'Failed to save Standard Operational. Please check document attachments and try again.',
                ], 500);
            }

            return back()->withInput()->with('toast', [
                'type' => 'error',
                'message' => 'Failed to save Standard Operational. Please try again.',
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StandardOperational $standardOperational)
    {
        $user = auth()->user();
        
        // For non-super admin users, verify access
        if (!$user->hasRole('super_admin')) {
            $selectedCompanyId = session('selected_company_id');
            
            // Verify standard operational belongs to their company
            if ($standardOperational->company_id !== $selectedCompanyId) {
                abort(403, 'Unauthorized access to this standard operational.');
            }
        }
        
        // Load relationships
        $standardOperational->load(['company', 'categories', 'documents']);
        
        return view('admin.standard-operationals.detail', compact('standardOperational'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StandardOperational $standardOperational)
    {
        $user = auth()->user();
        
        // Super Admin sees all companies and categories
        if ($user->hasRole('super_admin')) {
            $companies = Company::all();
            $categories = Category::all();
        } else {
            // Admin/Viewer only see their company and categories
            $selectedCompanyId = session('selected_company_id');
            
            // Verify standard operational belongs to their company
            if ($standardOperational->company_id !== $selectedCompanyId) {
                abort(403, 'Unauthorized access to this standard operational.');
            }
            
            $company = Company::find($selectedCompanyId);
            $companies = $company ? collect([$company]) : collect([]);
            $categories = Category::where('company_id', $selectedCompanyId)->get();
        }
        
        $standardOperational->load('categories');
        return view('admin.standard-operationals.edit', compact('standardOperational', 'companies', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStandardOperationalRequest $request, StandardOperational $standardOperational)
    {
        try {
            // Closure-based transaction
            DB::transaction(function () use ($request, $standardOperational) {
                $validated = $request->validated();
                $categoryIds = $validated['category_ids'] ?? [];
                unset($validated['category_ids']);

                // Remove documents data from validated
                $documentNames = $request->input('document_names', []);
                $documentTypes = $request->input('document_types', []);
                $documentFiles = $request->file('documents', []);
                $existingDocumentIds = $request->input('existing_document_ids', []);
                
                $standardOperational->update($validated);
                $standardOperational->categories()->sync($categoryIds);
                
                // Handle documents - delete documents not in the list
                if (!empty($existingDocumentIds)) {
                    $standardOperational->documents()->whereNotIn('id', $existingDocumentIds)->delete();
                } else {
                    // Delete all documents if none are submitted
                    $standardOperational->documents()->delete();
                }
                
                // Add new documents
                if (!empty($documentNames) && !empty($documentFiles)) {
                    foreach ($documentNames as $index => $name) {
                        if (isset($documentFiles[$index])) {
                            $file = $documentFiles[$index];
                            $originalName = $file->getClientOriginalName();
                            $attachmentPath = $file->storeAs('documents', $originalName, 'public');
                            
                            $standardOperational->documents()->create([
                                'name' => $name,
                                'type' => $documentTypes[$index] ?? null,
                                'attachment' => $attachmentPath,
                            ]);
                        }
                    }
                }
                
                // Log audit trail
                $this->logAuditTrail('Updated Standard Operational', "Updated SOP: {$standardOperational->title}");
            });

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Standard Operational updated successfully.',
                    'redirect' => route('admin.standard-operationals.index', [], false),
                ]);
            }

            return redirect()->route('admin.standard-operationals.index')->with('toast', ['type' => 'success', 'message' => 'Standard Operational updated successfully.']);
        } catch (Throwable $e) {
            Log::error('Failed to update standard operational', [
                'standard_operational_id' => $standardOperational->id,
                'error' => $e->getMessage(),
                'document_names_count' => count($request->input('document_names', [])),
                'document_files_count' => count($request->file('documents', [])),
                'user_id' => optional(auth()->user())->id,
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => config('app.debug')
                        ? $e->getMessage()
                        : 'Failed to update Standard Operational. Please check document attachments and try again.',
                ], 500);
            }

            return back()->withInput()->with('toast', [
                'type' => 'error',
                'message' => 'Failed to update Standard Operational. Please try again.',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StandardOperational $standardOperational)
    {
        $standardOperationalTitle = $standardOperational->title;
        
        // Closure-based transaction
        DB::transaction(function () use ($standardOperational, $standardOperationalTitle) {
            $standardOperational->delete();
            
            // Log audit trail
            $this->logAuditTrail('Deleted Standard Operational', "Deleted SOP: {$standardOperationalTitle}");
        });

        return redirect()->route('admin.standard-operationals.index')->with('toast', ['type' => 'success', 'message' => 'Standard Operational deleted successfully.']);
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
