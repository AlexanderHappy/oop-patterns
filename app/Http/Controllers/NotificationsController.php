<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendNotificationRequest;
use Illuminate\Http\JsonResponse;

class NotificationsController
{
    public function send(SendNotificationRequest $request): JsonResponse
    {
        $strategy = $this->createNotificationStrategy(
            $request->input('channel'),
            [
                $request->input('recipient'),
                $request->input('message'),
                $request->input('priority'),
            ]
        );
    }

    private function createNotificationStrategy(string $channel, array $data)
    {
        switch ($channel) {
            case 'credit_card':
                return app('payment.strategy.creditcard', [
                    'card_number' => $data['card_number'] ?? '',
                    'cvv' => $data['cvv'] ?? ''
                ]);

            case 'paypal':
                return app('payment.strategy.paypal', [
                    'email' => $data['email'] ?? ''
                ]);

            case 'crypto':
                return app('payment.strategy.crypto', [
                    'wallet' => $data['wallet_address'] ?? ''
                ]);

            default:
                throw new \Exception('Unknown payment method');
        }
    }
}
