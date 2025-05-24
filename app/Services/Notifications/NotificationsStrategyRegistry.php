<?php

namespace App\Services\Notifications;

use App\Interfaces\NotificationsStrategyInterface;
use ReflectionClass;

class NotificationsStrategyRegistry
{
    private array $strategies = [];

    public function register(
        string $method,
        string $strategyClass
    ): void
    {
        $this->strategies[$method] = $strategyClass;
    }

    /**
     * @throws \ReflectionException
     */
    public function create(
        string $method,
        array  $data = []
    ): NotificationsStrategyInterface
    {
        if (!isset($this->strategies[$method])) {
            throw new \Exception("Unknown notification method: {$method}");
        }

        $reflection = new ReflectionClass($this->strategies[$method]);
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            return $reflection->newInstance();
        }

        $params = [];
        foreach ($constructor->getParameters() as $param) {
            $paramName = $param->getName();
            $params[] = $data[$paramName] ?? (
                $param->isDefaultValueAvailable() ? $param->getDefaultValue() : ''
            );
        }

        return $reflection->newInstanceArgs($params);
    }
}
