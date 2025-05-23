<?php

namespace App\Services;

use App\Interfaces\PaymentStrategyInterface;
use Exception;
use ReflectionClass;

class PaymentStrategyRegistry
{
    private array $strategies = [];

    public function register(string $method, string $strategyClass): void
    {
        $this->strategies[$method] = $strategyClass;
    }

    /**
     * @throws \ReflectionException
     */
    public function create(string $method, array $data = []): PaymentStrategyInterface
    {
        if (!isset($this->strategies[$method])) {
            throw new \RuntimeException("Unknown payment method: {$method}");
        }

        return (new ReflectionClass($this->strategies[$method]))->newInstanceArgs($data);
    }

    public function getAvailableMethods(): array
    {
        return array_keys($this->strategies);
    }

    public function hasMethod(string $method): bool
    {
        return isset($this->strategies[$method]);
    }
}
