<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{

    use LogsAuditTrail;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::orderBy('id')->get();
        return view('admin.companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        try {
            // Closure-based transaction
            DB::transaction(function () use ($request) {
                $validated = $request->validated();
                $company = Company::create($validated);
                
                // Log audit trail
                $this->logAuditTrail('Created Company', "Created company: {$company->name}");
            });

            // Set session toast for redirect
            session()->flash('toast', ['type' => 'success', 'message' => 'Company created successfully.']);

            // Return success JSON for AJAX to trigger redirect
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            return redirect()->route('admin.companies.index');

        } catch (\Exception $e) {

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to create Company.'], 500);
            }
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => 'Failed to create Company.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        return response()->json($company);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        return view('admin.companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        try {
            $oldName = $company->name;
            
            // Closure-based transaction
            DB::transaction(function () use ($request, $company, $oldName) {
                $validated = $request->validated();
                $company->update($validated);
                
                // Log audit trail
                $this->logAuditTrail('Updated Company', "Updated company from '{$oldName}' to '{$company->name}'");
            });

            // Set session toast for redirect
            session()->flash('toast', ['type' => 'success', 'message' => 'Company updated successfully.']);

            // Return success JSON for AJAX to trigger redirect
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            return redirect()->route('admin.companies.index');

        } catch (\Exception $e) {
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to update Company.'], 500);
            }
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => 'Failed to update Company.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $companyName = $company->name;
        
        // Closure-based transaction
        DB::transaction(function () use ($company, $companyName) {
            $company->delete();
            
            // Log audit trail
            $this->logAuditTrail('Deleted Company', "Deleted company: {$companyName}");
        });

        return redirect()->route('admin.companies.index')->with('toast', ['type' => 'success', 'message' => 'Company deleted successfully.']);
    }
}
