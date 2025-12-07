<?php

namespace App\Services;

use GuzzleHttp\Client;
use Google\Auth\Credentials\ServiceAccountCredentials;

class AutoBiographyService
{
    protected $client;
    protected $endpoint;
    protected $projectId;

    public function __construct()
    {
        $this->projectId = env('GCP_PROJECT_ID');
        $this->endpoint = $this->endpoint = "https://us-central1-aiplatform.googleapis.com/v1/projects/{$this->projectId}/locations/us-central1/publishers/google/models/gemini-1.5-flash:generateContent";
        $this->client = new Client();
    }

    public function generateFromPrompt(string $prompt): string
    {
        $apiKey = env('GOOGLE_GEMINI_API_KEY'); // .env에 API 키 저장
        $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

        $response = $this->client->post($endpoint, [
            'headers' => [
                'Content-Type' => 'application/json',
                'X-goog-api-key' => $apiKey,
            ],
            'json' => [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
            ],
        ]);

        $body = json_decode($response->getBody(), true);

        return $body['candidates'][0]['content']['parts'][0]['text'] ?? 'AI 응답 없음';
    }

    public function buildAutobiographyPrompt(array $sections): string
    {
        $lines = array_map(function ($section) {
            return "- {$section['title']}: {$section['content']}";
        }, $sections);

        $formatted = implode("\n", $lines);

        return <<<EOT
            당신은 사용자의 생애 이야기를 감성적이고 정제된 자서전 형식으로 풀어주는 AI 작가입니다.
            다음 내용을 참고하여 자연스럽고 문학적인 자서전 문단을 3~5개 생성해 주세요:

        $formatted

            자서전 스타일로 써 주세요.
        EOT;
    }

    protected function getAccessToken(): string
    {
        $jsonKeyPath = base_path(env('GOOGLE_APPLICATION_CREDENTIALS'));

        $scopes = ['https://www.googleapis.com/auth/cloud-platform'];
        $creds = new ServiceAccountCredentials($scopes, $jsonKeyPath);
        $token = $creds->fetchAuthToken();

        if (!isset($token['access_token'])) {
            throw new \Exception('Access token could not be retrieved.');
        }
        return $token['access_token'];
    }
}
