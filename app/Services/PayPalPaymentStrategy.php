<?php

namespace App\Services;

use App\Interfaces\PaymentStrategyInterface;

class PayPalPaymentStrategy implements PaymentStrategyInterface
{

    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function pay(float $amount): array
    {
        return [
            'status' => 'success',
            'transaction_id' => 'pp_' . uniqid('', true),
            'method' => 'paypal',
            'amount' => $amount,
            'fee' => $amount * 0.034, // 3.4% комиссия
            'message' => "Оплата {$amount} руб. через PayPal ({$this->email})"
        ];
    }

    public function getName(): string
    {
        return 'PayPal';
    }

    public function isAvailable(): bool
    {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
