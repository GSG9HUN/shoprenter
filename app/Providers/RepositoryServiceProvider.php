<?php

namespace App\Providers;

use App\Interfaces\SecretRepositoryInterface;
use App\Repositories\SecretRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(SecretRepositoryInterface::class, SecretRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
