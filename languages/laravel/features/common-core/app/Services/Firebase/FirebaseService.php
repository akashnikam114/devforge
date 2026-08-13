<?php

namespace App\Services\Firebase;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;
use Exception;
use App\Helpers\BusinessSettingHelper;

class FirebaseService
{
    private $projectId;
    private $jsonKeyPath;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id') ?: BusinessSettingHelper::getBusinessInfo('firebase_project_id');
        $credentialsPath = config('firebase.credentials');
        $this->jsonKeyPath = $credentialsPath
            ? base_path($credentialsPath)
            : storage_path('app/public/' . $this->projectId . '-firebase-adminsdk.json');
    }

    public function sendPushNotification(array $notification)
    {
        try {
            $accessToken = $this->getAccessToken();
            $imageUrl = !empty($notification['image']) ? asset('storage/app/public/' . $notification['image']) : null;

            $recipientKey = !empty($notification['firebase_token']) ? 'token' : 'topic';
            $recipientValue = !empty($notification['firebase_token']) ? $notification['firebase_token'] : ($notification['topic'] ?? 'all_users');

            $payload = [
                'message' => [
                    $recipientKey => $recipientValue,
                    'notification' => [
                        'title' => $notification['title'],
                        'body' => $notification['description'],
                        'image' => $imageUrl,
                    ],
                    'data' => [
                        'title' => $notification['title'],
                        'body' => $notification['description'],
                        'image' => $imageUrl,
                    ],
                    'android' => [
                        'notification' => [
                            'image' => $imageUrl
                        ],
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'mutable-content' => 1,
                                'sound' => 'default'
                            ],
                        ],
                        'fcm_options' => [
                            'image' => $imageUrl,
                        ],
                    ],
                ],
            ];

            $response = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send", $payload);

            return $response->json();
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function getAccessToken()
    {
        if (!file_exists($this->jsonKeyPath)) {
            throw new Exception('Firebase config file is missing');
        }

        $credentials = new ServiceAccountCredentials(
            'https://www.googleapis.com/auth/firebase.messaging',
            $this->jsonKeyPath
        );

        $token = $credentials->fetchAuthToken();
        return $token['access_token'] ?? throw new Exception('Failed to obtain access token');
    }
}
