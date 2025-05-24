<?php

namespace App\Interfaces;

interface PaymentStrategyInterface
{
    public function pay(
        float $amount
    ): array;

    public function getName(): string;

    public function isAvailable(): bool;
}
