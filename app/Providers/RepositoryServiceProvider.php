<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\AeronaveRepository;
use App\Services\RankingService;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AeronaveRepository::class, function ($app) {
            return new AeronaveRepository();
        });
        
        $this->app->singleton(RankingService::class, function ($app) {
            return new RankingService($app->make(AeronaveRepository::class));
        });
    }
    
    public function boot(): void
    {
        //
    }
}