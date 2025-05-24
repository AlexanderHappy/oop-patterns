<?php

namespace App\Services\Notifications;

use App\Attributes\NotificationsStrategy;
use App\Attributes\PaymentStrategy;
use App\Interfaces\NotificationsStrategyInterface;

#[NotificationsStrategy('email')]
class EmailNotificationStrategy implements NotificationsStrategyInterface
{
    private string $recipient;
    private string $message;
    private string $priority;

    public function __construct(
        string $recipient,
        string $message,
        string $priority,
    )
    {
        $this->recipient = $recipient;
        $this->message = $message;
        $this->priority = $priority;
    }

    public function send(): array
    {
        return [
            "recipient" => $this->recipient,
            "message"   => $this->message,
            "channel"   => "email",
            "priority"  => $this->priority,
        ];
    }

    public function getChannelName(): string
    {
        // TODO: Implement getChannelName() method.
    }

    public function isAvailable(): bool
    {
        return isset($this->recipient) && isset($this->message);
    }

    public function getDeliveryTime(): int
    {
        // TODO: Implement getDeliveryTime() method.
    }
}
