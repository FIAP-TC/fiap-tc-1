<?php

namespace App\Providers;

use App\Services\OrderApprovalTokenService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(OrderApprovalTokenService::class, function ($app) {
            return new OrderApprovalTokenService(config('services.order_approval.secret'));
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
