# 예외 처리 규칙

## 기본 원칙

### 예외 처리
- 모든 예외는 적절히 처리
- 사용자에게 명확한 에러 메시지 제공
- 로깅을 통한 디버깅 정보 보존

## Try-Catch 블록

### 기본 구조
- 데이터베이스 트랜잭션이 있는 경우 try-catch 사용
- 예외 발생 시 롤백 처리

```php
try {
    DB::beginTransaction();

    // 데이터베이스 작업들
    
    DB::commit();

    return response()->json([
        'result' => 'success',
        'message' => '처리가 완료되었습니다.'
    ]);
} catch (Exception $e) {
    DB::rollBack();

    return response()->json([
        'result' => 'fail',
        'message' => '처리에 실패하였습니다. ['.$e->getMessage().']'
    ]);
}
```

## 예외 타입

### 일반 예외
- `Exception` 클래스 사용
- 일반적인 예외 처리

```php
use Exception;

try {
    // 로직
} catch (Exception $e) {
    // 처리
}
```

### 특정 예외
- 필요 시 특정 예외 타입 처리

```php
try {
    // 로직
} catch (ValidationException $e) {
    // 검증 예외 처리
} catch (Exception $e) {
    // 기타 예외 처리
}
```

## 에러 메시지

### 사용자 메시지
- 한국어로 명확한 메시지 제공
- 기술적 세부사항은 최소화

```php
return response()->json([
    'result' => 'fail',
    'message' => '기념관 생성에 실패하였습니다. ['.$e->getMessage().']'
]);
```

### 디버깅 정보
- 개발 환경에서만 상세 정보 제공
- 프로덕션에서는 일반적인 메시지만

```php
$message = '기념관 생성에 실패하였습니다.';
if (config('app.debug')) {
    $message .= ' ['.$e->getMessage().']';
}

return response()->json([
    'result' => 'fail',
    'message' => $message
]);
```

## HTTP 상태 코드

### 상태 코드 사용
- 예외 타입에 따라 적절한 상태 코드 반환

```php
// 400 Bad Request
return response()->json([...], Response::HTTP_BAD_REQUEST);

// 401 Unauthorized
return response()->json([...], Response::HTTP_UNAUTHORIZED);

// 404 Not Found
return response()->json([...], Response::HTTP_NOT_FOUND);

// 500 Internal Server Error
return response()->json([...], Response::HTTP_INTERNAL_SERVER_ERROR);
```

## 예외 발생

### 명시적 예외
- 비즈니스 로직에서 예외 발생

```php
if (!isset($token['access_token'])) {
    throw new \Exception('Access token could not be retrieved.');
}
```

### 검증 예외
- Laravel의 ValidationException 사용

```php
if ($validator->fails()) {
    throw new ValidationException($validator);
}
```

## 로깅

### 예외 로깅
- 중요한 예외는 로그에 기록

```php
try {
    // 로직
} catch (Exception $e) {
    \Log::error('기념관 생성 실패', [
        'user_id' => Auth::id(),
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);

    return response()->json([
        'result' => 'fail',
        'message' => '기념관 생성에 실패하였습니다.'
    ]);
}
```

## 예외 핸들러

### 글로벌 핸들러
- `app/Exceptions/Handler.php`에서 전역 예외 처리
- 특정 예외 타입에 대한 커스텀 처리

```php
public function register()
{
    $this->reportable(function (Throwable $e) {
        // 예외 처리 로직
    });
}
```

## 파일 업로드 예외

### 업로드 실패
```php
try {
    $uploadResponse = $this->cloudinary->uploadApi()->upload(...);
} catch (Exception $e) {
    return response()->json([
        'result' => 'fail',
        'message' => '업로드가 실패하였습니다. ['.$e->getMessage().']'
    ]);
}
```

## 외부 API 예외

### HTTP 요청 실패
```php
$response = Http::post($endpoint, $data);

if ($response->getStatusCode() != 200) {
    return response()->json([
        'code' => $response->getStatusCode(),
        'message' => 'Http request error'
    ]);
}
```

## 데이터베이스 예외

### 트랜잭션 실패
- 트랜잭션 내 예외 발생 시 자동 롤백
- 명시적 롤백 호출

```php
try {
    DB::beginTransaction();
    
    // 작업들
    
    DB::commit();
} catch (Exception $e) {
    DB::rollBack();
    // 에러 처리
}
```

## 예외 체이닝

### 원본 예외 보존
- 필요 시 원본 예외 정보 보존

```php
try {
    // 로직
} catch (Exception $e) {
    \Log::error('처리 실패', [
        'original' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    throw new \Exception('처리 중 오류가 발생했습니다.', 0, $e);
}
```
