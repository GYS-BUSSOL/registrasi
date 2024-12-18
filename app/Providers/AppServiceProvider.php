<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
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

        // Definisikan gate berdasarkan kolom 'usr_access'
        Gate::define('access-admin', function ($user) {
            return $user->usr_access === 'admin';  // Mengecek apakah usr_access adalah 'admin'
        });

        Gate::define('access-hr', function ($user) {
            return $user->usr_access === 'hr';  // Mengecek apakah usr_access adalah 'hr'
        });

        Gate::define('access-admin-or-hr', function ($user) {
            return in_array($user->usr_access, ['admin', 'hr']);
        });

        Gate::define('access-operator', function ($user) {
            return $user->usr_access === 'operator';  // Mengecek apakah usr_access adalah 'operator'
        });

        Gate::define('access-admin-or-operator', function ($user) {
            return in_array($user->usr_access, ['admin', 'operator']);
        });
    }
}
