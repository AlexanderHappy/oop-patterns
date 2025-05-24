<?php

namespace App\Services\Payments;

use App\Attributes\PaymentStrategy;
use App\Interfaces\PaymentStrategyInterface;

#[PaymentStrategy('credit_card')]
class CreditCardPaymentStrategy implements PaymentStrategyInterface
{
    private string $cardNumber;
    private string $cvv;

    public function __construct(string $cardNumber, string $cvv)
    {
        $this->cardNumber = $cardNumber;
        $this->cvv = $cvv;
    }

    public function pay(float $amount): array
    {
        // Логика оплаты кредитной картой
        return [
            'status' => 'success',
            'transaction_id' => 'cc_' . uniqid('', true),
            'method' => 'credit_card',
            'amount' => $amount,
            'fee' => $amount * 0.029, // 2.9% комиссия
            'message' => "Оплата {$amount} руб. картой **** " . substr($this->cardNumber, -4)
        ];
    }

    public function getName(): string
    {
        return 'Credit Card';
    }

    public function isAvailable(): bool
    {
        return !empty($this->cardNumber) && !empty($this->cvv);
    }
}
