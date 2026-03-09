<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('super_admin')) {
                return true;
            }
        });

        // Add custom body class for viewer role
        View::composer('*', function ($view) {
            if (auth()->check() && auth()->user()->hasRole('viewer')) {
                config(['adminlte.classes_body' => 'viewer-layout ' . config('adminlte.classes_body')]);
            }
        });

        // Share viewer layout CSS with all adminlte views
        View::composer('adminlte::page', function ($view) {
            if (auth()->check() && auth()->user()->hasRole('viewer')) {
                $view->with('viewerLayoutCss', asset('css/viewer-layout.css'));
            }
        });
    }
}
