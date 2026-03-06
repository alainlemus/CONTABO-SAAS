<?php

namespace App\Providers;

use App\Listeners\HandlePaymentFailed;
use App\Listeners\HandlePaymentMethodUpdated;
use App\Listeners\HandlePaymentSucceeded;
use App\Listeners\HandleSubscriptionActivated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Events\WebhookReceived;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Event::listen(WebhookReceived::class, HandlePaymentFailed::class);
        Event::listen(WebhookReceived::class, HandleSubscriptionActivated::class);
        Event::listen(WebhookReceived::class, HandlePaymentSucceeded::class);
        Event::listen(WebhookReceived::class, HandlePaymentMethodUpdated::class);
    }
}
