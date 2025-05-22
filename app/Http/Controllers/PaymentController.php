<?php

namespace App\Http\Controllers;

use App\Interfaces\PaymentStrategyInterface;
use App\Services\PaymentProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

readonly class PaymentController
{
    public function __construct(
        private PaymentProcessor $paymentProcessor
    )
    {
    }

    public function processPayment(Request $request): JsonResponse
    {
        // TODO Переместить в отдельный Request класс
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:credit_card,paypal,crypto',
            'payment_data' => 'required|array'
        ]);

        try {
            $strategy = $this->createPaymentStrategy(
                $request->input('method'),
                $request->input('payment_data')
            );

            $this->paymentProcessor->setStrategy($strategy);
            $result = $this->paymentProcessor->processPayment($request->input('amount'));

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getAvailableMethods(): JsonResponse
    {
        $methods = $this->paymentProcessor->getAvailableMethods();

        return response()->json([
            'methods' => array_map(function($method) {
                return $method->getName();
            }, $methods)
        ]);
    }

    private function createPaymentStrategy(string $method, array $data): PaymentStrategyInterface
    {
        switch ($method) {
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
