<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Policies\FilePolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
    ];
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
         Gate::define('view-file', [FilePolicy::class, 'view']);
    }
}
