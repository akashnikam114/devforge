<?php

namespace App\Services\Razorpay;

use GuzzleHttp\Client;

class RazorpayAutopayService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function createMandate(array $data): array
    {
        return [
            'success' => false,
            'message' => 'Razorpay autopay implementation pending.',
            'data' => $data,
        ];
    }
}
