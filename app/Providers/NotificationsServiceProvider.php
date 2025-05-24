<?php

namespace App\Providers;

use App\Attributes\NotificationsStrategy;
use App\Services\Notifications\NotificationsStrategyRegistry;
use Illuminate\Support\ServiceProvider;
use ReflectionClass;

class NotificationsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $registry = new NotificationsStrategyRegistry();

        foreach ($this->scanStrategiesNotifications() as $class => $method) {
            $registry->register($method, $class);
        }

        $this->app->instance(NotificationsStrategyRegistry::class, $registry);
    }

    private function scanStrategiesNotifications(): array
    {
        $strategies = [];
        $path = app_path('Services');

        $files = glob($path . '/Notifications/*NotificationStrategy.php');

        foreach ($files as $file) {
            $className = $this->getClassNameFromFile($file);

            if (!class_exists($className)) {
                continue;
            }

            $reflection = new ReflectionClass($className);
            $attributes = $reflection->getAttributes(NotificationsStrategy::class);

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
        return "App\\Services\\Notifications\\$basename";
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
