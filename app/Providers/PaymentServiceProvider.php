<?php

namespace App\Providers;

use App\Services\CreditCardPaymentStrategy;
use App\Services\CryptoPaymentStrategy;
use App\Services\PaymentProcessor;
use App\Services\PayPalPaymentStrategy;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('payment.processor', function () {
            return new PaymentProcessor();
        });

        // Регистрация стратегий
        $this->app->bind('payment.strategy.creditcard', function ($app, $params) {
            return new CreditCardPaymentStrategy(
                $params['card_number'] ?? '',
                $params['cvv'] ?? ''
            );
        });

        $this->app->bind('payment.strategy.paypal', function ($app, $params) {
            return new PayPalPaymentStrategy(
                $params['email'] ?? ''
            );
        });

        $this->app->bind('payment.strategy.crypto', function ($app, $params) {
            return new CryptoPaymentStrategy(
                $params['wallet'] ?? ''
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
