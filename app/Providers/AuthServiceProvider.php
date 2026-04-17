<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Defina suas gates aqui se necessário
        Gate::define('admin', function ($user) {
            return $user->tipo == 0; // 0 = admin
        });
        
        Gate::define('user', function ($user) {
            return $user->tipo == 1; // 1 = usuário comum
        });
    }
}