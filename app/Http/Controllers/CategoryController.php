<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Company;
use App\Models\Category;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    use LogsAuditTrail;
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Super Admin sees all categories and companies
        if ($user->hasRole('super_admin')) {
            $companies = Company::all();
            $categories = Category::with('company')->orderBy('id')->get();
        } else {
            // Admin/Viewer only see categories and company from their company
            $selectedCompanyId = session('selected_company_id');
            $company = Company::find($selectedCompanyId);
            $companies = $company ? collect([$company]) : collect([]);
            $categories = Category::where('company_id', $selectedCompanyId)
                ->with('company')
                ->orderBy('id')
                ->get();
        }
        
        return view('admin.categories.index', compact('categories', 'companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Super Admin sees all companies
        if ($user->hasRole('super_admin')) {
            $companies = Company::all();
        } else {
            // Admin/Viewer only see their company
            $selectedCompanyId = session('selected_company_id');
            $company = Company::find($selectedCompanyId);
            $companies = $company ? collect([$company]) : collect([]);
        }
        
        return view('admin.categories.create', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            // Closure-based transaction
            DB::transaction(function () use ($request) {
                $validated = $request->validated();
                $category = Category::create($validated);
                
                // Log audit trail
                $this->logAuditTrail('Created Category', "Created category: {$category->name}");
            });

            // Set session toast for redirect
            session()->flash('toast', ['type' => 'success', 'message' => 'Category created successfully.']);

            // Return success JSON for AJAX to trigger redirect
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            return redirect()->route('admin.categories.index');

        } catch (\Exception $e) {

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to create Category.'], 500);
            }
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => 'Failed to create Category.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return response()->json($category);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $user = auth()->user();
        
        // Super Admin sees all companies
        if ($user->hasRole('super_admin')) {
            $companies = Company::all();
        } else {
            // Admin/Viewer only see their company
            $selectedCompanyId = session('selected_company_id');
            
            // Verify category belongs to their company
            if ($category->company_id !== $selectedCompanyId) {
                abort(403, 'Unauthorized access to this category.');
            }
            
            $company = Company::find($selectedCompanyId);
            $companies = $company ? collect([$company]) : collect([]);
        }
        
        return view('admin.categories.edit', compact('category', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $oldName = $category->name;
            
            // Closure-based transaction
            DB::transaction(function () use ($request, $category, $oldName) {
                $validated = $request->validated();
                $category->update($validated);
                
                // Log audit trail
                $this->logAuditTrail('Updated Category', "Updated category from '{$oldName}' to '{$category->name}'");
            });

            // Set session toast for redirect
            session()->flash('toast', ['type' => 'success', 'message' => 'Category updated successfully.']);

            // Return success JSON for AJAX to trigger redirect
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            return redirect()->route('admin.categories.index');

        } catch (\Exception $e) {
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to update Category.'], 500);
            }
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => 'Failed to update Category.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $categoryName = $category->name;
        
        // Closure-based transaction
        DB::transaction(function () use ($category, $categoryName) {
            $category->delete();
            
            // Log audit trail
            $this->logAuditTrail('Deleted Category', "Deleted category: {$categoryName}");
        });

        return redirect()->route('admin.categories.index')->with('toast', ['type' => 'success', 'message' => 'Category deleted successfully.']);
    }

    /**
     * Get categories by Company Id.
     */
    public function getCategoriesByCompany(Request $request)
    {
        $companyId = $request->input('company_id');
        
        if (!$companyId) {
            return response()->json([]);
        }
        
        $categories = Category::where('company_id', $companyId)
            ->orderBy('name')
            ->get(['id', 'name']);
        
        return response()->json($categories);
    }
}
