<?php

use App\Http\Controllers\Api\V1\PaymentGatewayWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('payment-gateways')->group(function () {
    Route::post('razorpay/webhook', [PaymentGatewayWebhookController::class, 'razorpay']);
    Route::post('easebuzz/webhook', [PaymentGatewayWebhookController::class, 'easebuzz']);
    Route::post('phonepe/webhook', [PaymentGatewayWebhookController::class, 'phonepe']);
});
