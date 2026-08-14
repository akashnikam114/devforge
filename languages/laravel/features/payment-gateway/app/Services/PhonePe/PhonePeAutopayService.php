<?php

namespace App\Services\PhonePe;

use GuzzleHttp\Client;

class PhonePeAutopayService
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
            'message' => 'PhonePe autopay implementation pending.',
            'data' => $data,
        ];
    }
}
