# Laravel 코딩 표준

## 컨트롤러

### 기본 구조
- 모든 API 컨트롤러는 `App\Http\Controllers\API` 네임스페이스에 위치
- `Controller` 클래스를 상속
- 메서드명은 동사로 시작 (register, login, edit, delete 등)

### 의존성 주입
- 생성자에서 필요한 서비스나 의존성을 주입
- Laravel의 서비스 컨테이너 활용

```php
protected $cloudinary;

public function __construct(Cloudinary $cloudinary)
{
    $this->cloudinary = $cloudinary;
}
```

### Request 처리
- `Request` 객체를 통해 입력 데이터 처리
- `request()->only()` 메서드로 필요한 필드만 추출

```php
$data = request()->only('user_name', 'birth_start', 'birth_end');
```

## 모델

### 테이블명
- 커스텀 테이블명 사용 시 `$table` 속성 명시
- 테이블명은 `mm_` 접두사 사용 (예: `mm_users`, `mm_memorials`)

```php
protected $table = 'mm_memorials';
```

### 관계 정의
- Eloquent 관계 메서드 사용
- 관계 메서드명은 명확하고 의미 있게 작성

```php
public function attachmentProfileImage()
{
    return $this->hasOne(Attachment::class, 'id', 'profile_attachment_id')
        ->where('is_delete', 0);
}
```

### Fillable/Hidden
- Mass Assignment 보호를 위해 `$fillable` 또는 `$guarded` 사용
- 민감한 정보는 `$hidden`에 추가

## 서비스

### 서비스 클래스
- 비즈니스 로직은 서비스 클래스로 분리
- `App\Services` 네임스페이스 사용
- 컨트롤러는 서비스를 호출하여 로직 처리

```php
namespace App\Services;

class ExampleService
{
    public function processData(array $data): array
    {
        // 비즈니스 로직
    }
}
```

## 환경 변수

### .env 사용
- 환경 변수는 `.env` 파일에 저장
- `env()` 헬퍼 함수 사용
- 설정 파일(`config/`)을 통한 접근 권장

```php
// 권장
$value = config('services.cloudinary.api_key');

// 허용
$value = env('CLOUDINARY_API_KEY');
```

## 데이터베이스

### 트랜잭션
- 여러 데이터베이스 작업이 있을 경우 트랜잭션 사용
- `DB::beginTransaction()`, `DB::commit()`, `DB::rollBack()` 사용

```php
try {
    DB::beginTransaction();
    
    // 데이터베이스 작업
    
    DB::commit();
} catch (Exception $e) {
    DB::rollBack();
    // 에러 처리
}
```

### 쿼리 빌더
- Eloquent ORM 우선 사용
- 복잡한 쿼리는 쿼리 빌더 사용
- Raw 쿼리는 최소화

## 라우팅

### API 라우트
- `routes/api.php`에 API 라우트 정의
- RESTful 리소스 네이밍 사용
- 미들웨어 그룹 활용

```php
Route::middleware('auth:api')->prefix('memorial')->group(function() {
    Route::post('/register', [MemorialController::class, 'register']);
    Route::get('/view', [MemorialController::class, 'view']);
});
```

## 인증

### Passport
- Laravel Passport 사용
- `auth:api` 미들웨어로 API 인증
- 토큰 기반 인증

```php
Route::middleware('auth:api')->group(function() {
    // 인증이 필요한 라우트
});
```
