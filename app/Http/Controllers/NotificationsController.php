<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendNotificationRequest;
use App\Interfaces\NotificationsStrategyInterface;
use App\Services\Notifications\NotificationsManager;
use App\Services\Notifications\NotificationsStrategyRegistry;
use Illuminate\Http\JsonResponse;

readonly class NotificationsController
{
    public function __construct(
        private NotificationsManager          $notificationsManager,
        private NotificationsStrategyRegistry $notificationsStrategyRegistry
    )
    {
    }

    /**
     * @throws \ReflectionException
     */
    public function send(
        SendNotificationRequest $request
    ): JsonResponse
    {
        $strategy = $this->createNotificationStrategy(
            $request->input('channel'),
            [
                'recipient' => $request->input('recipient'),
                'message'   => $request->input('message'),
                'priority'  => $request->input('priority'),
            ],
        );
        $this->notificationsManager->setStrategy($strategy);
        $result = $this->notificationsManager->sendNotification();

        return response()->json(
            $result
        );
    }

    /**
     * @throws \ReflectionException
     */
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
