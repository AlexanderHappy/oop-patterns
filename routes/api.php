<?php

use App\Http\Controllers\NotificationsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::prefix('payment')->group(function () {
    Route::post('/process', [PaymentController::class, 'processPayment']);
    Route::get('/methods', [PaymentController::class, 'getAvailableMethods']);
});

Route::prefix('notifications')->group(function () {
    Route::post('/process', [NotificationsController::class, 'send']);
});
