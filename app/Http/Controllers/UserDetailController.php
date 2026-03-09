<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserDetailRequest;
use App\Http\Requests\UpdateUserDetailRequest;
use App\Models\User;
use App\Models\Company;
use App\Models\Category;
use App\Models\UserDetail;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserDetailController extends Controller
{
    use LogsAuditTrail;
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Super Admin sees all users
        if ($user->hasRole('super_admin')) {
            $users = User::with(['userDetail.company', 'roles'])->orderBy('id')->get();
        } else {
            // Admin only sees users from their company
            $selectedCompanyId = session('selected_company_id');
            $users = User::with(['userDetail.company', 'roles'])
                ->whereHas('userDetail', function ($query) use ($selectedCompanyId) {
                    $query->where('company_id', $selectedCompanyId);
                })
                ->orderBy('id')
                ->get();
        }
        
        return view('admin.user-details.index', compact('users'));
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
            // Admin only sees their company and categories
            $selectedCompanyId = session('selected_company_id');
            $company = Company::find($selectedCompanyId);
            $companies = $company ? collect([$company]) : collect([]);
            $categories = Category::where('company_id', $selectedCompanyId)->get();
        }
        
        return view('admin.user-details.create', compact('companies', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserDetailRequest $request)
    {
        // Closure-based transaction
        DB::transaction(function () use ($request) {
            $validated = $request->validated();
            $categoryIds = $validated['category_ids'] ?? [];
            unset($validated['category_ids']);

            // Check if there's a soft deleted user with the same email
            $existingUser = User::withTrashed()->where('email', $validated['email'])->first();

            if ($existingUser && $existingUser->trashed()) {
                // Restore the soft deleted user and update its data
                $existingUser->restore();
                $existingUser->update([
                    'name' => $request->input('name'),
                    'password' => bcrypt($request->input('password')),
                ]);
                $user = $existingUser;
                
                // Update or create user detail
                $userDetail = UserDetail::withTrashed()->where('user_id', $user->id)->first();
                if ($userDetail) {
                    if ($userDetail->trashed()) {
                        $userDetail->restore();
                    }
                    $userDetail->update([
                        'email' => $validated['email'],
                        'category_user' => $validated['category_user'],
                        'company_id' => $validated['company_id'],
                    ]);
                } else {
                    $userDetail = UserDetail::create([
                        'email' => $validated['email'],
                        'category_user' => $validated['category_user'],
                        'company_id' => $validated['company_id'],
                        'user_id' => $user->id,
                    ]);
                }
            } else {
                // Create new user
                $user = User::create([
                    'name' => $request->input('name'),
                    'email' => $validated['email'],
                    'password' => bcrypt($request->input('password')),
                ]);
                
                // Save user detail and relation
                $userDetail = UserDetail::create([
                    'email' => $validated['email'],
                    'category_user' => $validated['category_user'],
                    'company_id' => $validated['company_id'],
                    'user_id' => $user->id,
                ]);
            }

            // Sync role and categories
            $user->syncRoles([$validated['category_user']]);
            $userDetail->categories()->sync($categoryIds);
            
            // Log audit trail
            $this->logAuditTrail('Created User', "Created user: {$user->name} ({$user->email})");
        });

        return redirect()->route('admin.user-details.index')->with('toast', ['type' => 'success', 'message' => 'User Detail created successfully.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserDetail $userDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserDetail $userDetail)
    {
        $user = auth()->user();
        
        // Super Admin sees all companies and categories
        if ($user->hasRole('super_admin')) {
            $companies = Company::all();
            $categories = Category::all();
        } else {
            // Admin only sees their company and categories
            $selectedCompanyId = session('selected_company_id');
            
            // Verify user detail belongs to their company
            if ($userDetail->company_id !== $selectedCompanyId) {
                abort(403, 'Unauthorized access to this user detail.');
            }
            
            $company = Company::find($selectedCompanyId);
            $companies = $company ? collect([$company]) : collect([]);
            $categories = Category::where('company_id', $selectedCompanyId)->get();
        }
        
        $selectedCategories = $userDetail->categories->pluck('id')->toArray();
        return view('admin.user-details.edit', compact('userDetail', 'companies', 'categories', 'selectedCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserDetailRequest $request, UserDetail $userDetail)
    {
        // Closure-based transaction
        DB::transaction(function () use ($request, $userDetail) {
            $validated = $request->validated();
            $categoryIds = $validated['category_ids'] ?? [];
            unset($validated['category_ids']);

            // Update main user if there are changes in email or password
            $user = $userDetail->user;
            $userData = [
                'email' => $validated['email'],
                'name' => $validated['name'],
            ];
            if (!empty($validated['password'])) {
                $userData['password'] = bcrypt($validated['password']);
            }
            $user->update($userData);

            // Sync user role based on category user
            $user->syncRoles([$validated['category_user']]);

            // Update user detail
            unset($validated['password']);
            
            if (isset($validated['name'])) {
                unset($validated['name']);
            }
            $userDetail->update($validated);
            $userDetail->categories()->sync($categoryIds);
            
            // Log audit trail
            $this->logAuditTrail('Updated User', "Updated user: {$user->name} ({$user->email})");
        });
        
        return redirect()->route('admin.user-details.index')->with('toast', ['type' => 'success', 'message' => 'User Detail updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserDetail $userDetail)
    {
        $userName = $userDetail->user->name;
        $userEmail = $userDetail->user->email;
        
        // Closure-based transaction
        DB::transaction(function () use ($userDetail, $userName, $userEmail) {
            // Detach pivot relations first
            if (method_exists($userDetail, 'categories')) {
                $userDetail->categories()->detach();
            }

            // Keep reference to related user then delete detail
            $user = $userDetail->user;
            $userDetail->delete();

            // Also delete the related user if exists
            if ($user) {
                $user->delete();
            }
            
            // Log audit trail
            $this->logAuditTrail('Deleted User', "Deleted user: {$userName} ({$userEmail})");
        });

        return redirect()->route('admin.user-details.index')->with('toast', ['type' => 'success', 'message' => 'User Detail deleted successfully.']);
    }
}
