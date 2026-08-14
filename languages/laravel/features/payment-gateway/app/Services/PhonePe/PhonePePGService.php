<?php

namespace App\Services\PhonePe;

use GuzzleHttp\Client;

class PhonePePGService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function initiatePayment(array $data): array
    {
        return [
            'success' => false,
            'message' => 'PhonePe PG implementation pending.',
            'data' => $data,
        ];
    }
}
