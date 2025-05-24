<?php

namespace App\Console\Commands;

use App\Services\Payments\CreditCardPaymentStrategy;
use App\Services\Payments\CryptoPaymentStrategy;
use App\Services\Payments\PaymentProcessor;
use App\Services\Payments\PayPalPaymentStrategy;
use Illuminate\Console\Command;

class ProcessPaymentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:process {amount} {method} {--card-number=} {--cvv=} {--email=} {--wallet=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process payment using different strategies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $processor = new PaymentProcessor();
        $amount = (float) $this->argument('amount');
        $method = $this->argument('method');

        try {
            switch ($method) {
                case 'card':
                    $strategy = new CreditCardPaymentStrategy(
                        $this->option('card-number'),
                        $this->option('cvv')
                    );
                    break;

                case 'paypal':
                    $strategy = new PayPalPaymentStrategy($this->option('email'));
                    break;

                case 'crypto':
                    $strategy = new CryptoPaymentStrategy($this->option('wallet'));
                    break;

                default:
                    $this->error('Unknown payment method');
                    return 1;
            }

            $processor->setStrategy($strategy);
            $result = $processor->processPayment($amount);

            $this->info("Payment processed successfully:");
            $this->table(
                ['Field', 'Value'],
                [
                    ['Status', $result['status']],
                    ['Transaction ID', $result['transaction_id']],
                    ['Method', $result['method']],
                    ['Amount', $result['amount'] . ' руб.'],
                    ['Fee', $result['fee'] . ' руб.'],
                    ['Message', $result['message']]
                ]
            );

        } catch (\Exception $e) {
            $this->error('Payment failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
