<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;

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
        Gate::define('admin', function (User $user) {
            $result = $user->isAdmin();
            \Log::info('Auth check for admin role', [
                'user' => $user->email,
                'role' => $user->role,
                'result' => $result
            ]);
            return $result;
        });

        Gate::define('user', function (User $user) {
            $result = $user->isUser();
            \Log::info('Auth check for user role', [
                'user' => $user->email,
                'role' => $user->role,
                'result' => $result
            ]);
            return $result;
        });
    }
}