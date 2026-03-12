<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePolicyLetterRequest;
use App\Http\Requests\UpdatePolicyLetterRequest;
use App\Models\Company;
use App\Models\Category;
use App\Models\PolicyLetter;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class PolicyLetterController extends Controller
{

    use LogsAuditTrail;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Super Admin sees all policy letters
        if ($user->hasRole('super_admin')) {
            $policyLetters = PolicyLetter::with(['categories', 'company'])->orderBy('id')->get();
        } else {
            // Admin/Viewer only see policy letters from their company
            $selectedCompanyId = session('selected_company_id');
            $policyLetters = PolicyLetter::where('company_id', $selectedCompanyId)
                ->with(['categories', 'company'])
                ->orderBy('id')
                ->get();
        }
        
        return view('admin.policy-letters.index', compact('policyLetters'));
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
        
        return view('admin.policy-letters.create', compact('companies', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePolicyLetterRequest $request)
    {
        try {
            // Closure-based transaction
            DB::transaction(function () use ($request) {
                $validated = $request->validated();
                $categoryIds = $validated['category_ids'] ?? [];
                unset($validated['category_ids']);

                // Remove documents data from validated
                $documentNames = $request->input('document_names', []);
                $documentFiles = $request->file('documents', []);
                
                $policyLetter = PolicyLetter::create($validated);
                $policyLetter->categories()->sync($categoryIds);
                
                // Handle documents
                if (!empty($documentNames) && !empty($documentFiles)) {
                    foreach ($documentNames as $index => $name) {
                        if (isset($documentFiles[$index])) {
                            $file = $documentFiles[$index];
                            $originalName = $file->getClientOriginalName();
                            $attachmentPath = $file->storeAs('documents', $originalName, 'public');
                            
                            $policyLetter->documents()->create([
                                'name' => $name,
                                'attachment' => $attachmentPath,
                            ]);
                        }
                    }
                }
                
                // Log audit trail
                $this->logAuditTrail('Created Policy Letter', "Created Policy Letter: {$policyLetter->title}");
            });

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Policy Letter created successfully.',
                    'redirect' => route('admin.policy-letters.index', [], false),
                ]);
            }

            return redirect()->route('admin.policy-letters.index')->with('toast', ['type' => 'success', 'message' => 'Policy Letter created successfully.']);
        } catch (Throwable $e) {
            Log::error('Failed to store policy letter', [
                'error' => $e->getMessage(),
                'document_names_count' => count($request->input('document_names', [])),
                'document_files_count' => count($request->file('documents', [])),
                'user_id' => optional(auth()->user())->id,
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => config('app.debug')
                        ? $e->getMessage()
                        : 'Failed to save Policy Letter. Please check document attachments and try again.',
                ], 500);
            }

            return back()->withInput()->with('toast', [
                'type' => 'error',
                'message' => 'Failed to save Policy Letter. Please try again.',
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PolicyLetter $policyLetter)
    {
        $user = auth()->user();
        
        // For non-super admin users, verify access
        if (!$user->hasRole('super_admin')) {
            $selectedCompanyId = session('selected_company_id');
            
            // Verify policy letter belongs to their company
            if ($policyLetter->company_id !== $selectedCompanyId) {
                abort(403, 'Unauthorized access to this policy letter.');
            }
        }
        
        // Load relationships
        $policyLetter->load(['company', 'categories', 'documents']);
        
        return view('admin.policy-letters.detail', compact('policyLetter'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PolicyLetter $policyLetter)
    {
        $user = auth()->user();
        
        // Super Admin sees all companies and categories
        if ($user->hasRole('super_admin')) {
            $companies = Company::all();
            $categories = Category::all();
        } else {
            // Admin/Viewer only see their company and categories
            $selectedCompanyId = session('selected_company_id');
            
            // Verify policy letter belongs to their company
            if ($policyLetter->company_id !== $selectedCompanyId) {
                abort(403, 'Unauthorized access to this policy letter.');
            }
            
            $company = Company::find($selectedCompanyId);
            $companies = $company ? collect([$company]) : collect([]);
            $categories = Category::where('company_id', $selectedCompanyId)->get();
        }
        
        $policyLetter->load('categories');
        return view('admin.policy-letters.edit', compact('policyLetter', 'companies', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePolicyLetterRequest $request, PolicyLetter $policyLetter)
    {
        try {
        // Closure-based transaction
            DB::transaction(function () use ($request, $policyLetter) {
                $validated = $request->validated();
                $categoryIds = $validated['category_ids'] ?? [];
                unset($validated['category_ids']);

                // Remove documents data from validated
                $documentNames = $request->input('document_names', []);
                $documentFiles = $request->file('documents', []);
                $existingDocumentIds = $request->input('existing_document_ids', []);
                
                $policyLetter->update($validated);
                $policyLetter->categories()->sync($categoryIds);
                
                // Handle documents - delete documents not in the list
                if (!empty($existingDocumentIds)) {
                    $policyLetter->documents()->whereNotIn('id', $existingDocumentIds)->delete();
                } else {
                    // Delete all documents if none are submitted
                    $policyLetter->documents()->delete();
                }
                
                // Add new documents
                if (!empty($documentNames) && !empty($documentFiles)) {
                    foreach ($documentNames as $index => $name) {
                        if (isset($documentFiles[$index])) {
                            $file = $documentFiles[$index];
                            $originalName = $file->getClientOriginalName();
                            $attachmentPath = $file->storeAs('documents', $originalName, 'public');
                            
                            $policyLetter->documents()->create([
                                'name' => $name,
                                'attachment' => $attachmentPath,
                            ]);
                        }
                    }
                }
                
                // Log audit trail
                $this->logAuditTrail('Updated Policy Letter', "Updated Policy Letter: {$policyLetter->title}");
            });

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Policy Letter updated successfully.',
                    'redirect' => route('admin.policy-letters.index', [], false),
                ]);
            }

            return redirect()->route('admin.policy-letters.index')->with('toast', ['type' => 'success', 'message' => 'Policy Letter updated successfully.']);
        } catch (Throwable $e) {
            Log::error('Failed to update policy letter', [
                'policy_letter_id' => $policyLetter->id,
                'error' => $e->getMessage(),
                'document_names_count' => count($request->input('document_names', [])),
                'document_files_count' => count($request->file('documents', [])),
                'user_id' => optional(auth()->user())->id,
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => config('app.debug')
                        ? $e->getMessage()
                        : 'Failed to update Policy Letter. Please check document attachments and try again.',
                ], 500);
            }

            return back()->withInput()->with('toast', [
                'type' => 'error',
                'message' => 'Failed to update Policy Letter. Please try again.',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PolicyLetter $policyLetter)
    {
        $policyLetterTitle = $policyLetter->title;
        
        // Closure-based transaction
        DB::transaction(function () use ($policyLetter, $policyLetterTitle) {
            $policyLetter->delete();
            
            // Log audit trail
            $this->logAuditTrail('Deleted Policy Letter', "Deleted Policy Letter: {$policyLetterTitle}");
        });

        return redirect()->route('admin.policy-letters.index')->with('toast', ['type' => 'success', 'message' => 'Policy Letter deleted successfully.']);
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
