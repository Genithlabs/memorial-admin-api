# 서비스 아키텍처

## 기본 원칙

### 책임 분리
- 서비스 클래스는 비즈니스 로직을 담당
- 컨트롤러는 HTTP 요청/응답 처리에만 집중
- 재사용 가능한 로직은 서비스로 분리

### 구조
```php
<?php

namespace App\Services;

class ExampleService
{
    protected $dependency;

    public function __construct($dependency)
    {
        $this->dependency = $dependency;
    }

    public function processData(array $data): array
    {
        // 비즈니스 로직 구현
        return $result;
    }
}
```

## 서비스 위치

### 네임스페이스
- 모든 서비스는 `App\Services` 네임스페이스에 위치
- 파일명은 클래스명과 동일

```
app/Services/
├── AutoBiographyService.php
└── ExampleService.php
```

## 의존성 주입

### 생성자 주입
- 필요한 의존성은 생성자에서 주입
- Laravel의 서비스 컨테이너 활용

```php
protected $client;
protected $endpoint;
protected $projectId;

public function __construct()
{
    $this->projectId = env('GCP_PROJECT_ID');
    $this->endpoint = "https://...";
    $this->client = new Client();
}
```

### 외부 API 클라이언트
- HTTP 클라이언트는 서비스 내부에서 생성
- GuzzleHttp\Client 등 사용

```php
use GuzzleHttp\Client;

protected $client;

public function __construct()
{
    $this->client = new Client();
}
```

## 환경 변수

### env() 사용
- 환경 변수는 `env()` 헬퍼로 접근
- 설정 파일을 통한 접근도 가능

```php
$apiKey = env('GOOGLE_GEMINI_API_KEY');
$projectId = env('GCP_PROJECT_ID');
```

## 메서드 설계

### 단일 책임
- 각 메서드는 하나의 명확한 작업만 수행
- 메서드명은 동사로 시작

```php
public function generateFromPrompt(string $prompt): string
{
    // 프롬프트로부터 생성
}

public function buildAutobiographyPrompt(array $sections): string
{
    // 자서전 프롬프트 구성
}
```

### 반환 타입
- 명확한 반환 타입 지정
- 타입 힌팅 적극 활용

```php
public function processData(array $data): array
{
    return [];
}

public function getAccessToken(): string
{
    return $token;
}
```

## 예외 처리

### 예외 발생
- 비즈니스 로직 오류 시 예외 발생
- 명확한 에러 메시지 제공

```php
if (!isset($token['access_token'])) {
    throw new \Exception('Access token could not be retrieved.');
}
```

### 예외 처리 위치
- 서비스에서는 예외를 발생시키고
- 컨트롤러에서 예외를 처리

## 외부 API 통신

### API 호출
- HTTP 클라이언트를 사용한 외부 API 호출
- 헤더, 바디 등 설정

```php
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
```

## 프롬프트 생성

### 템플릿 문자열
- HEREDOC 또는 NOWDOC 사용
- 가독성 있는 프롬프트 구성

```php
return <<<EOT
당신은 사용자의 생애 이야기를 감성적이고 정제된 자서전 형식으로 풀어주는 AI 작가입니다.
다음 내용을 참고하여 자연스럽고 문학적인 자서전 문단을 3~5개 생성해 주세요:

$formatted

자서전 스타일로 써 주세요.
EOT;
```

## 컨트롤러에서 사용

### 의존성 주입
- 컨트롤러 생성자에서 서비스 주입
- 또는 메서드에서 직접 주입

```php
use App\Services\AutoBiographyService;

class AutoBiographyController extends Controller
{
    protected $service;

    public function __construct(AutoBiographyService $service)
    {
        $this->service = $service;
    }

    public function generate(Request $request)
    {
        $result = $this->service->generateFromPrompt($prompt);
        // 응답 처리
    }
}
```
