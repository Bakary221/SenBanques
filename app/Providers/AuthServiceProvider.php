<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Configuration Passport
        Passport::loadKeysFrom(storage_path('oauth'));
        Passport::tokensExpireIn(now()->addDays(7));
        Passport::refreshTokensExpireIn(now()->addDays(14));

        // Définir les scopes disponibles
        Passport::tokensCan([
            'view-accounts' => 'Voir les comptes bancaires',
            'create-accounts' => 'Créer des comptes bancaires',
            'manage-accounts' => 'Gérer tous les comptes bancaires',
            'view-transactions' => 'Voir les transactions',
            'create-transactions' => 'Créer des transactions',
            'manage-transactions' => 'Gérer toutes les transactions',
            'manage-users' => 'Gérer les utilisateurs',
        ]);

        // Gates pour les permissions
        Gate::define('view-accounts', function ($user) {
            return in_array($user->role ?? 'client', ['client', 'admin']);
        });

        Gate::define('manage-accounts', function ($user) {
            return ($user->role ?? 'client') === 'admin';
        });

        Gate::define('view-transactions', function ($user) {
            return in_array($user->role ?? 'client', ['client', 'admin']);
        });

        Gate::define('manage-transactions', function ($user) {
            return in_array($user->role ?? 'client', ['client', 'admin']);
        });

        Gate::define('manage-users', function ($user) {
            return ($user->role ?? 'client') === 'admin';
        });
    }
}
