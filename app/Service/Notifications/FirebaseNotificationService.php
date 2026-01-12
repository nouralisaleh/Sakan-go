<?php

namespace App\Service\Notifications;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;

class FirebaseNotificationService
{
    protected string $projectId;
    protected string $credentialsPath;

    public function __construct()
    {
        $this->credentialsPath = config('firebase.credentials');
        $this->projectId = json_decode(
            file_get_contents($this->credentialsPath),
            true
        )['project_id'];
    }

    protected function getAccessToken(): string
    {
        $client = new GoogleClient();
        $client->setAuthConfig($this->credentialsPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $token = $client->fetchAccessTokenWithAssertion();
        return $token['access_token'];
    }

    public function send(string $fcmToken, string $title, string $body, array $data = []): void
    {
        if (!$fcmToken) return;

        $accessToken = $this->getAccessToken();

        Http::withToken($accessToken)
            ->post(
                "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send",
                [
                    'message' => [
                        'token' => $fcmToken,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'data' => $data,
                    ],
                ]
            );
    }
}
