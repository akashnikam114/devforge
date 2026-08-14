<?php

namespace App\Services\Easebuzz;

use GuzzleHttp\Client;

class EasebuzzAutopayService
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
            'message' => 'Easebuzz autopay implementation pending.',
            'data' => $data,
        ];
    }
}
