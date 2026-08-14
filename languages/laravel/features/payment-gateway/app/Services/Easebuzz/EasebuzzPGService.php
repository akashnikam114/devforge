<?php

namespace App\Services\Easebuzz;

use GuzzleHttp\Client;

class EasebuzzPGService
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
            'message' => 'Easebuzz PG implementation pending.',
            'data' => $data,
        ];
    }
}
