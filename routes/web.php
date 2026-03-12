<?php

use App\Models\Category;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StandardOperationalController;
use App\Http\Controllers\PolicyLetterController;
use App\Http\Controllers\UserDetailController;
use App\Http\Controllers\AuditTrailController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
})->middleware('auth');

Route::get('/dashboard', function () {
    $user = auth()->user();
    $selectedCompanyId = session('selected_company_id');

    // Super Admin sees all categories from all companies
    if ($user && $user->hasRole('super_admin')) {
        $categories = Category::with(['standardOperationals.formDocuments', 'policyLetters', 'company'])
            ->withCount(['standardOperationals', 'policyLetters'])
            ->get();
    }

    // Viewer sees only their assigned categories filtered by company
    elseif ($user && $user->hasRole('viewer') && $user->userDetail && $user->userDetail->categories()->exists()) {
        $categories = $user->userDetail->categories()
            ->where('company_id', $selectedCompanyId)
            ->with(['standardOperationals' => function ($query) use ($selectedCompanyId) {
                $query->where('company_id', $selectedCompanyId)->with('formDocuments');
            }, 'policyLetters' => function ($query) use ($selectedCompanyId) {
                $query->where('company_id', $selectedCompanyId);
            }, 'company'])
            ->withCount(['standardOperationals', 'policyLetters'])
            ->get();
    }
    
    // Admin sees all categories but only from their company
    else {
        $categories = Category::where('company_id', $selectedCompanyId)
            ->with(['standardOperationals' => function ($query) use ($selectedCompanyId) {
                $query->where('company_id', $selectedCompanyId)->with('formDocuments');
            }, 'policyLetters' => function ($query) use ($selectedCompanyId) {
                $query->where('company_id', $selectedCompanyId);
            }, 'company'])
            ->withCount(['standardOperationals', 'policyLetters'])
            ->get();
    }
    
    return view('dashboard', compact('categories'));
    
})->middleware(['auth', 'verified', 'company.selected'])->name('dashboard');

Route::middleware(['auth', 'company.selected'])->group(function () {
    // Route::post('/profile', [ProfileController::class, 'store'])->name('profile.store');
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('admin')->name('admin.')->group(function () {

        Route::middleware('can:manage user details')->group(function () {
            Route::resource('user-details', UserDetailController::class);
        });

        Route::middleware('can:manage companies')->group(function () {
            Route::resource('companies', CompanyController::class);
        });
    
        Route::middleware('can:manage categories')->group(function () {
            Route::resource('categories', CategoryController::class);
            Route::get('categories-by-company', [CategoryController::class, 'getCategoriesByCompany'])->name('categories.by-company');
        });

        Route::middleware('can:manage standard operationals')->group(function () {
            Route::resource('standard-operationals', StandardOperationalController::class);
            Route::post('upload-summernote-image', [StandardOperationalController::class, 'uploadSummernoteImage'])->name('upload-summernote-image');
        });

        Route::middleware('can:manage policy letters')->group(function () {
            Route::resource('policy-letters', PolicyLetterController::class);
            Route::post('upload-summernote-image', [PolicyLetterController::class, 'uploadSummernoteImage'])->name('upload-summernote-image');
        });

        Route::middleware('can:manage audit trails')->group(function () {
            Route::get('audit-trails', [AuditTrailController::class, 'index'])->name('audit-trails.index');
        });

    });
});

require __DIR__.'/auth.php';
