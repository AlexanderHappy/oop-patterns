<?php

namespace App\Interfaces;

interface NotificationsStrategyInterface
{
    public function send(
        string $recipient,
        string $message
    ): array;

    public function getChannelName(): string;

    public function isAvailable(): bool;

    public function getDeliveryTime(): int;
}
