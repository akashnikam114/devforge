<?php

namespace App\Services\Razorpay;

use GuzzleHttp\Client;

class RazorpayPGService
{
    public const BASE_URL = 'https://api.razorpay.com';

    protected Client $client;
    protected ?string $key;
    protected ?string $secret;

    public function __construct()
    {
        $this->client = new Client();
        $this->key = config('payment-gateway.razorpay.key');
        $this->secret = config('payment-gateway.razorpay.secret');
    }

    public function createOrder(array $data): array
    {
        return [
            'success' => false,
            'message' => 'Razorpay PG createOrder implementation pending.',
            'data' => $data,
        ];
    }
}
