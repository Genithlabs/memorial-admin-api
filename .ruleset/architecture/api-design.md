# API 디자인 규칙

## 기본 원칙

### RESTful 원칙
- 가능한 한 RESTful 원칙 준수
- 리소스 중심 설계
- HTTP 메서드 적절히 사용

### 엔드포인트 네이밍
- 명사 사용 (동사 지양)
- 소문자와 하이픈 사용
- 계층 구조 명확히

```
GET    /api/memorial/index          # 목록 조회
GET    /api/memorial/{id}/detail    # 상세 조회
POST   /api/memorial/register       # 등록
POST   /api/memorial/{id}/edit      # 수정
GET    /api/memorial/view           # 내 기념관 조회
```

## 라우트 구조

### 그룹화
- 관련된 라우트는 그룹으로 묶기
- 미들웨어 그룹 활용
- Prefix 사용

```php
Route::middleware('auth:api')->prefix('memorial')->name('memorial.')->group(function() {
    Route::post('/register', [MemorialController::class, 'register'])->name('register');
    Route::post('{id}/edit', [MemorialController::class, 'edit'])->name('edit');
    Route::get('/view', [MemorialController::class, 'view'])->name('view');
});
```

### 인증 미들웨어
- 인증이 필요한 라우트는 `auth:api` 미들웨어 사용
- 일부 라우트만 공개하려면 `withoutMiddleware` 사용

```php
Route::middleware('auth:api')->group(function() {
    // 인증 필요
    
    Route::withoutMiddleware('auth:api')->group(function() {
        // 인증 불필요
        Route::get('index', [MemorialController::class, 'index']);
    });
});
```

## 요청 형식

### Content-Type
- JSON 요청: `Content-Type: application/json`
- 파일 업로드: `multipart/form-data`

### 요청 본문
- 필요한 필드만 포함
- 일관된 필드명 사용 (snake_case)

```json
{
    "user_name": "홍길동",
    "birth_start": "1990-01-01",
    "birth_end": "2024-01-01",
    "career": "생애 내용"
}
```

## 응답 형식

### 표준 응답 구조
- 모든 API 응답은 일관된 구조 사용
- `result`, `message`, `data` 필드 포함

```json
{
    "result": "success",
    "message": "처리가 완료되었습니다.",
    "data": {
        "id": 1
    }
}
```

### 성공 응답
```php
return response()->json([
    'result' => 'success',
    'message' => '기념관 생성에 성공하였습니다.',
    'data' => [
        'id' => $memorial->id
    ]
]);
```

### 실패 응답
```php
return response()->json([
    'result' => 'fail',
    'message' => '기념관 생성에 실패하였습니다.'
], Response::HTTP_BAD_REQUEST);
```

### 에러 응답
```php
return response()->json([
    'result' => 'fail',
    'message' => $validator->errors()->all()
], Response::HTTP_BAD_REQUEST);
```

## HTTP 상태 코드

### 사용 규칙
- `200 OK`: 성공
- `400 Bad Request`: 잘못된 요청 (유효성 검증 실패)
- `401 Unauthorized`: 인증 실패
- `404 Not Found`: 리소스 없음
- `500 Internal Server Error`: 서버 오류

### 상태 코드 사용 예
```php
// 성공
return response()->json([...]); // 기본 200

// 유효성 검증 실패
return response()->json([...], Response::HTTP_BAD_REQUEST); // 400

// 인증 실패
return response()->json([...], Response::HTTP_UNAUTHORIZED); // 401
```

## 파일 업로드

### 엔드포인트
- 파일 업로드 전용 엔드포인트 제공
- 업로드 후 URL 반환

```php
Route::post('/upload', [MemorialController::class, 'upload'])->name('upload');
```

### 응답 형식
```json
{
    "result": "success",
    "message": "업로드가 성공하였습니다.",
    "url": "https://..."
}
```

## 인증

### 토큰 기반 인증
- Laravel Passport 사용
- `Authorization: Bearer {token}` 헤더

### 토큰 발급
- 로그인/회원가입 시 토큰 발급
- Passport 토큰 엔드포인트 사용

```php
$response = Http::asForm()->post($tokenRoute, [
    'grant_type' => 'password',
    'client_id' => $client->id,
    'client_secret' => $client->secret,
    'username' => $data['user_id'],
    'password' => $data['user_password'],
    'scope' => '*'
]);
```

## 버전 관리

### API 버전
- 현재는 버전 없이 `/api` 사용
- 향후 버전 관리 필요 시 `/api/v1` 형태 고려

## 문서화

### 주석
- 라우트 파일에 주석 추가
- 컨트롤러 메서드에 PHPDoc 주석

```php
/**
 * 기념관을 등록합니다.
 *
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */
public function register(Request $request)
{
    // 구현
}
```
