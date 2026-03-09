<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanySelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Super Admin bypass company validation
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        // Auto-set company from user detail if not already set
        if (!session()->has('selected_company_id')) {
            if ($user->userDetail && $user->userDetail->company_id) {
                session(['selected_company_id' => $user->userDetail->company_id]);
            } else {
                // If user has no company assigned, abort
                abort(403, 'No company assigned to your account. Please contact administrator.');
            }
        }

        return $next($request);
    }
}
