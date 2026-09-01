<?php

namespace App\Providers;

use App\Domain\ServiceOrder\Notifications\OrderApprovalNotifierInterface;
use App\Domain\ServiceOrder\Security\OrderApprovalTokenSigner;
use App\Infrastructure\Notifications\OrderApprovalMailNotifier;
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
        $this->app->singleton(OrderApprovalTokenSigner::class, function ($app) {
            return new OrderApprovalTokenSigner(config('services.order_approval.secret'));
        });

        $this->app->bind(OrderApprovalNotifierInterface::class, OrderApprovalMailNotifier::class);
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
