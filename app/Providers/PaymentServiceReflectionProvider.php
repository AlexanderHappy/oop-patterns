<?php

namespace App\Providers;

use App\Attributes\PaymentStrategy;
use App\Services\PaymentProcessor;
use App\Services\PaymentStrategyRegistry;
use Illuminate\Support\ServiceProvider;
use ReflectionClass;

class PaymentServiceReflectionProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentProcessor::class, function () {
            return new PaymentProcessor();
        });

        // Создаем и регистрируем реестр стратегий
        $registry = new PaymentStrategyRegistry();

        foreach ($this->scanPaymentStrategies() as $class => $method) {
            $registry->register($method, $class);
        }

        $this->app->instance(PaymentStrategyRegistry::class, $registry);
    }

    public function boot(): void
    {
        //
    }

    private function scanPaymentStrategies(): array
    {
        $strategies = [];
        $path = app_path('Services');

        $files = glob($path . '/*PaymentStrategy.php');

        foreach ($files as $file) {
            $className = $this->getClassNameFromFile($file);

            if (!class_exists($className)) {
                continue;
            }

            $reflection = new ReflectionClass($className);
            $attributes = $reflection->getAttributes(PaymentStrategy::class);

            if (!empty($attributes)) {
                $attribute = $attributes[0]->newInstance();
                $strategies[$className] = $attribute->method;
            }
        }

        return $strategies;
    }

    private function getClassNameFromFile(string $file): string
    {
        $basename = basename($file, '.php');
        return "App\\Services\\{$basename}";
    }
}
