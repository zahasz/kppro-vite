<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Role;
use App\Models\BankAccount;
use App\Policies\BankAccountPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        BankAccount::class => BankAccountPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Sprawdź, czy model Role jest poprawnie mapowany
        Gate::define('manage-roles', function (User $user) {
            return $user->hasRole('admin');
        });

        // Dostęp do panelu administratora
        Gate::define('access-admin-panel', function (User $user) {
            return $user->hasRole('admin');
        });

        // Automatyczne przyznawanie wszystkich uprawnień dla administratora
        Gate::before(function (User $user, $ability) {
            if ($user->hasRole('admin')) {
                return true;
            }
        });
    }
} 