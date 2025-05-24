<?php

namespace App\Services\Payments;

use App\Interfaces\PaymentStrategyInterface;

class PaymentProcessor
{
    private PaymentStrategyInterface $strategy;

    public function setStrategy(
        PaymentStrategyInterface $strategy
    ): void
    {
        $this->strategy = $strategy;
    }

    public function processPayment(
        float $amount
    ): array
    {
        if (!isset($this->strategy)) {
            throw new \Exception('Payment strategy not set');
        }

        if (!$this->strategy->isAvailable()) {
            throw new \Exception('Payment method is not available');
        }

        return $this->strategy->pay($amount);
    }

    public function getAvailableMethods(): array
    {
        // В реальном приложении это можно получать из конфигурации
        $strategies = [
            new CreditCardPaymentStrategy(
                '4111111111111111',
                '123'
            ),
            new PayPalPaymentStrategy('user@example.com'),
            new CryptoPaymentStrategy('1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa'),
        ];

        return array_filter(
            $strategies,
            function (
                $strategy
            ) {
                return $strategy->isAvailable();
            }
        );
    }
}
