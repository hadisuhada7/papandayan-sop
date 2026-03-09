<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    use LogsAuditTrail;
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        
        // Auto-set company from user detail (skip for super admin)
        $user = Auth::user();
        if (!$user->hasRole('super_admin') && $user->userDetail && $user->userDetail->company_id) {
            $request->session()->put('selected_company_id', $user->userDetail->company_id);
        }
        
        // Log audit trail for successful login
        $this->logAuditTrail('User Login', "User logged in: " . Auth::user()->name . " (" . Auth::user()->email . ")");

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Log audit trail before logout
        $userName = Auth::user()->name;
        $userEmail = Auth::user()->email;
        $this->logAuditTrail('User Logout', "User logged out: {$userName} ({$userEmail})");
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
