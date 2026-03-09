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
        // Closure-based transaction
        DB::transaction(function () use ($request) {
            $validated = $request->validated();
            $categoryIds = $validated['category_ids'] ?? [];
            unset($validated['category_ids']);

            // Remove documents data from validated
            $documentNames = $request->input('document_names', []);
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
                            'attachment' => $attachmentPath,
                        ]);
                    }
                }
            }
            
            // Log audit trail
            $this->logAuditTrail('Created Standard Operational', "Created SOP: {$standardOperational->title}");
        });

        return redirect()->route('admin.standard-operationals.index')->with('toast', ['type' => 'success', 'message' => 'Standard Operational created successfully.']);
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
        // Closure-based transaction
        DB::transaction(function () use ($request, $standardOperational) {
            $validated = $request->validated();
            $categoryIds = $validated['category_ids'] ?? [];
            unset($validated['category_ids']);

            // Remove documents data from validated
            $documentNames = $request->input('document_names', []);
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
                            'attachment' => $attachmentPath,
                        ]);
                    }
                }
            }
            
            // Log audit trail
            $this->logAuditTrail('Updated Standard Operational', "Updated SOP: {$standardOperational->title}");
        });

        return redirect()->route('admin.standard-operationals.index')->with('toast', ['type' => 'success', 'message' => 'Standard Operational updated successfully.']);
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
}
