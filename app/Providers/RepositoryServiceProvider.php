<?php
// app/Providers/RepositoryServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\AeronaveRepository;
use App\Repositories\AeroportoRepository; // Adicione
use App\Services\RankingService;
use App\Services\AeroportoService; // Adicione

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repositories
        $this->app->singleton(AeronaveRepository::class, function ($app) {
            return new AeronaveRepository();
        });
        
        $this->app->singleton(AeroportoRepository::class, function ($app) {
            return new AeroportoRepository();
        });
        
        // Services
        $this->app->singleton(RankingService::class, function ($app) {
            return new RankingService($app->make(AeronaveRepository::class));
        });
        
        $this->app->singleton(AeroportoService::class, function ($app) {
            return new AeroportoService($app->make(AeroportoRepository::class));
        });
    }
    
    public function boot(): void
    {
        //
    }
}