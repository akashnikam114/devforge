<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\RazorpayWebhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentGatewayWebhookController extends BaseApiController
{
    public function razorpay(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true) ?: $request->all();

        RazorpayWebhook::create([
            'event' => $payload['event'] ?? 'unknown',
            'payload_data' => $payload,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Razorpay webhook received.',
        ]);
    }

    public function easebuzz(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Easebuzz webhook endpoint ready.',
            'data' => $request->all(),
        ]);
    }

    public function phonepe(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'PhonePe webhook endpoint ready.',
            'data' => $request->all(),
        ]);
    }
}
