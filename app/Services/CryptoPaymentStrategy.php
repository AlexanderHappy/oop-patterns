<?php

namespace App\Services;

use App\Interfaces\PaymentStrategyInterface;

class CryptoPaymentStrategy implements PaymentStrategyInterface
{
    private string $walletAddress;

    public function __construct(string $walletAddress)
    {
        $this->walletAddress = $walletAddress;
    }

    public function pay(float $amount): array
    {
        return [
            'status' => 'pending',
            'transaction_id' => 'crypto_' . uniqid(),
            'method' => 'cryptocurrency',
            'amount' => $amount,
            'fee' => 0, // Без комиссии
            'message' => "Оплата {$amount} руб. криптовалютой на адрес {$this->walletAddress}"
        ];
    }

    public function getName(): string
    {
        return 'Cryptocurrency';
    }

    public function isAvailable(): bool
    {
        return !empty($this->walletAddress);
    }
}
