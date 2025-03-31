<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        // Logowanie zapytań SQL
        if (config('app.debug')) {
            DB::listen(function ($query) {
                \Log::info(
                    $query->sql,
                    [
                        'bindings' => $query->bindings,
                        'time' => $query->time
                    ]
                );
            });
        }

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

        // Dyrektywa Blade dla sprawdzania dostępu do modułu
        Blade::if('module', function ($moduleCode) {
            if (!auth()->check()) {
                return false;
            }
            
            $user = auth()->user();
            return $user->canAccessModule($moduleCode);
        });
    }
}
