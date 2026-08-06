<?php

namespace App\Services\Firebase;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;
use Exception;
use App\Helpers\BusinessSettingHelper;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    private $projectId;
    private $apiKey;
    private $jsonKeyPath;

    public function __construct()
    {
        $this->projectId = BusinessSettingHelper::getBusinessInfo('firebase_project_id');
        $this->apiKey = BusinessSettingHelper::getBusinessInfo('firebase_api_key');
        $this->jsonKeyPath = storage_path('app/public/' . $this->projectId . '-firebase-adminsdk.json');
    }

    public static function sendOtp(string $phoneNumber, string $recaptchaToken)
    {
        try {
            $instance = new self();
            $response = Http::post("https://identitytoolkit.googleapis.com/v1/accounts:sendVerificationCode?key=" . $instance->apiKey, [
                'phoneNumber' => '+91' . $phoneNumber,
                'recaptchaToken' => $recaptchaToken
            ]);

            return $response->successful() ? $response->json()['sessionInfo'] : null;
        } catch (Exception $e) {
            Log::error("Firebase sendOtp error: " . $e->getMessage());
            return null;
        }
    }

    public static function verifyOtp(string $sessionInfo, string $code): bool
    {
        try {
            $instance = new self();
            $response = Http::post("https://identitytoolkit.googleapis.com/v1/accounts:signInWithPhoneNumber?key=" . $instance->apiKey, [
                'sessionInfo' => $sessionInfo,
                'code' => $code
            ]);

            return $response->successful();
        } catch (Exception $e) {
            Log::error("Firebase verifyOtp error: " . $e->getMessage());
            return false;
        }
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
