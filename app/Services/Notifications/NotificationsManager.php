<?php

namespace App\Services\Notifications;

use App\Interfaces\NotificationsStrategyInterface;
use App\Interfaces\PaymentStrategyInterface;

class NotificationsManager
{
    private NotificationsStrategyInterface $strategy;

    public function setStrategy(
        NotificationsStrategyInterface $strategy
    ): void
    {
        $this->strategy = $strategy;
    }

    public function sendNotification(): array
    {
        if (!isset($this->strategy)) {
            throw new \Exception('Notification strategy not set');
        }

        if (!$this->strategy->isAvailable()) {
            throw new \Exception('Notification method is not available');
        }

        return $this->strategy->send();
    }
}
