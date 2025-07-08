<?php

namespace App\Providers;

use App\Http\Middleware\LogActivityMiddleware;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

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
        $this->registerPolicies();
        Passport::loadKeysFrom(base_path('/secrets/oauth'));
    }
}
