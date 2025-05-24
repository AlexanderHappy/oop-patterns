<?php

namespace App\Http\Controllers;

use App\Services\Payments\PaymentProcessor;
use App\Services\Payments\PaymentStrategyRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

readonly class PaymentController
{
    public function __construct(
        private PaymentProcessor        $paymentProcessor,
        private PaymentStrategyRegistry $registry
    )
    {
    }

    public function processPayment(
        Request $request
    ): JsonResponse
    {
        $request->validate(
            [
                'amount'       => 'required|numeric|min:0.01',
                'method'       => 'required|string',
                'payment_data' => 'required|array',
            ]
        );

        try {
            $strategy = $this->createPaymentStrategy(
                $request->input('method'),
                $request->input('payment_data')
            );

            $this->paymentProcessor->setStrategy($strategy);
            $result = $this->paymentProcessor->processPayment(
                $request->input('amount')
            );

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json(
                [
                    'status'  => 'error',
                    'message' => $e->getMessage(),
                ],
                400
            );
        }
    }

    public function getAvailableMethods(): JsonResponse
    {
        return response()->json(
            [
                'methods' => $this->registry->getAvailableMethods(),
            ]
        );
    }

    /**
     * @throws \ReflectionException
     */
    private function createPaymentStrategy(
        string $method,
        array  $data
    ): \App\Interfaces\PaymentStrategyInterface
    {
        return $this->registry->create(
            $method,
            $data
        );
    }
}
