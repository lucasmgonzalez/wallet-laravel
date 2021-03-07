<?php

namespace App\Providers;

use App\BusinessServices\Contracts\TransactionServiceContract;
use App\BusinessServices\TransactionService;
use App\Services\MockNotifierService;
use App\Services\MockTransactionAuthorizerService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Registering Services
        $this->app->singleton(MockNotifierService::class, function ($app) {
            return new MockNotifierService();
        });

        $this->app->singleton(MockTransactionAuthorizerService::class, function ($app) {
            return new MockTransactionAuthorizerService();
        });

        // Registering BusinessServices
        $this->app->bind(TransactionServiceContract::class, TransactionService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
