# 인증 보안 규칙

## Laravel Passport

### 기본 설정
- Laravel Passport를 사용한 OAuth2 인증
- 토큰 기반 인증 방식
- `auth:api` 미들웨어 사용

### 토큰 발급
- 로그인/회원가입 시 토큰 발급
- Password Grant 사용

```php
$client = Client::where('password_client', 1)->first();
$tokenRoute = env('APP_URL').route('passport.token', absolute: false);

$response = Http::asForm()->post($tokenRoute, [
    'grant_type' => 'password',
    'client_id' => $client->id,
    'client_secret' => $client->secret,
    'username' => $data['user_id'],
    'password' => $data['user_password'],
    'scope' => '*'
]);
```

## 인증 미들웨어

### 라우트 보호
- 인증이 필요한 라우트는 미들웨어 그룹 사용

```php
Route::middleware('auth:api')->group(function() {
    // 인증이 필요한 라우트
});
```

### 인증 확인
- 컨트롤러에서 현재 사용자 확인

```php
$userId = Auth::user()->id;
$user = Auth::user();
```

## 비밀번호 보안

### 해싱
- 비밀번호는 `bcrypt()` 또는 `Hash::make()` 사용
- 절대 평문으로 저장하지 않음

```php
'user_password' => bcrypt($data['user_password'])
// 또는
'user_password' => Hash::make($data['user_password'])
```

### 비밀번호 검증
- 로그인 시 `Auth::attempt()` 사용
- 비밀번호 확인은 해시 비교로 처리

```php
$credential = [
    'user_id' => $data['user_id'],
    'password' => $data['user_password'],
];

if (!Auth::attempt($credential)) {
    return response()->json([
        'message' => '유효하지 않은 사용자 정보 입니다.'
    ], Response::HTTP_UNAUTHORIZED);
}
```

## 사용자 인증 정보

### 커스텀 필드
- `user_id` 필드를 사용자명으로 사용
- `getAuthPassword()` 메서드로 비밀번호 필드 지정

```php
public function getAuthPassword(): string
{
    return $this->user_password;
}

public function findForPassport($userid)
{
    return $this->where('user_id', $userid)->first();
}
```

## 토큰 관리

### 토큰 저장
- 클라이언트 측에서 토큰 저장
- `Authorization: Bearer {token}` 헤더로 전송

### 토큰 갱신
- 토큰 만료 시 갱신 처리
- Refresh Token 사용 고려

## 권한 확인

### 리소스 소유권
- 리소스 수정/삭제 시 소유권 확인 필수

```php
$memorial = Memorial::where('id', $id)
    ->where('user_id', Auth::user()->id)
    ->first();

if (is_null($memorial)) {
    return response()->json([
        'result' => 'fail',
        'message' => '권한이 없습니다.'
    ]);
}
```

### 중복 생성 방지
- 사용자당 하나의 리소스만 생성 가능한 경우 확인

```php
$memorial = Memorial::where('user_id', Auth::user()->id)->first();
if (!is_null($memorial)) {
    return response()->json([
        'result' => 'fail',
        'message' => '이미 생성된 기념관이 존재합니다.'
    ]);
}
```

## 세션 관리

### API 특성
- API는 stateless이므로 세션 사용 안 함
- 토큰 기반 인증만 사용

## 보안 헤더

### CORS 설정
- `config/cors.php`에서 CORS 설정
- 필요한 도메인만 허용

### CSRF 보호
- API는 토큰 기반이므로 CSRF 보호 불필요
- `VerifyCsrfToken` 미들웨어에서 API 라우트 제외
