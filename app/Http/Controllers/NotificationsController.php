<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendNotificationRequest;
use App\Interfaces\NotificationsStrategyInterface;
use App\Services\Notifications\NotificationsProcessor;
use Illuminate\Http\JsonResponse;

readonly class NotificationsController
{
    public function __construct(
        private NotificationsProcessor        $notificationsProcessor,
        private NotificationsStrategyRegistry $notificationsStrategyRegistry
    )
    {
    }

    public function send(
        SendNotificationRequest $request
    ): JsonResponse
    {
        $strategy = $this->createNotificationStrategy(
            $request->input('channel'),
            [
                $request->input('recipient'),
                $request->input('message'),
                $request->input('priority'),
            ],
        );

        return response()->json(
            [
                true,
            ]
        );
    }

    private function createNotificationStrategy(
        string $method,
        array  $data
    ): \App\Interfaces\NotificationsStrategyInterface
    {
        return $this->notificationsStrategyRegistry->create(
            $method,
            $data
        );
    }
}
