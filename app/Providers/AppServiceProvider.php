<?php

namespace App\Providers;

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
        $this->app->singleton(MockNotifierService::class, function ($app) {
            return new MockNotifierService();
        });

        $this->app->singleton(MockTransactionAuthorizerService::class, function ($app) {
            return new MockTransactionAuthorizerService();
        });
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
