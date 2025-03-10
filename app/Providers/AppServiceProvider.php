<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;

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
        // Rejestracja dyrektyw Blade dla Spatie Permission
        Blade::if('role', function ($role) {
            return Auth::check() && Auth::user()->hasRole($role);
        });

        Blade::if('hasrole', function ($role) {
            return Auth::check() && Auth::user()->hasRole($role);
        });

        Blade::if('hasanyrole', function ($roles) {
            return Auth::check() && Auth::user()->hasAnyRole($roles);
        });

        Blade::if('hasallroles', function ($roles) {
            return Auth::check() && Auth::user()->hasAllRoles($roles);
        });
    }
}
