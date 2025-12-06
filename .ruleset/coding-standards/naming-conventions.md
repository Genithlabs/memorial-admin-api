# 네이밍 컨벤션

## 클래스명

### 컨트롤러
- 단수형 명사 + `Controller` 접미사
- 예: `MemorialController`, `AuthController`, `StoryController`

### 모델
- 단수형 명사 (PascalCase)
- 예: `User`, `Memorial`, `Story`, `Attachment`

### 서비스
- 명사 + `Service` 접미사
- 예: `AutoBiographyService`

### 미들웨어
- 동사 + 명사 형태
- 예: `Authenticate`, `VerifyCsrfToken`

## 메서드명

### 컨트롤러 메서드
- 동사로 시작 (소문자 camelCase)
- RESTful 액션: `index`, `show`, `store`, `update`, `destroy`
- 커스텀 액션: `register`, `login`, `edit`, `view`, `upload`

```php
public function register(Request $request) { }
public function edit(Request $request, $id) { }
public function view(Request $request) { }
```

### 모델 메서드
- 관계 메서드: 명사 형태 (예: `attachmentProfileImage`, `attachmentBgm`)
- 스코프 메서드: `scope` 접두사 (예: `scopeActive`)

### 서비스 메서드
- 동사로 시작
- 예: `generateFromPrompt`, `buildAutobiographyPrompt`

## 변수명

### 일반 변수
- camelCase 사용
- 의미 있는 이름 사용

```php
$userId = Auth::user()->id;
$memorial = Memorial::find($id);
$profileUrl = $request->file('profile');
```

### 상수
- 대문자와 언더스코어 사용
- 클래스 내부 상수는 `private` 또는 `protected`

```php
private $S3_PATH_PROFILE = "/memorial/profile/";
private $S3_PATH_BGM = "/memorial/bgm/";
```

### 배열 키
- snake_case 사용 (데이터베이스 컬럼명과 일치)
- 예: `user_id`, `user_name`, `birth_start`, `profile_attachment_id`

## 데이터베이스

### 테이블명
- 복수형, snake_case
- `mm_` 접두사 사용
- 예: `mm_users`, `mm_memorials`, `mm_stories`, `mm_attachments`

### 컬럼명
- snake_case
- 예: `user_id`, `user_name`, `birth_start`, `is_visible`, `created_at`

### 외래키
- 참조하는 테이블명_기본키 형태
- 예: `user_id`, `memorial_id`, `attachment_id`

### 인덱스명
- 의미 있는 이름 사용
- 예: `idx_user_email`, `idx_memorial_user_id`

## 파일명

### PHP 파일
- 클래스명과 동일 (PascalCase)
- 예: `MemorialController.php`, `AutoBiographyService.php`

### 마이그레이션 파일
- Laravel 마이그레이션 네이밍 규칙 준수
- 예: `2024_03_01_154118_create_mm_memorials_table.php`

## 라우트명

### API 라우트
- 리소스명.액션 형태
- 예: `memorial.register`, `memorial.edit`, `user.login`

```php
Route::post('/register', [MemorialController::class, 'register'])
    ->name('memorial.register');
```

## 주석 및 문서

### 한국어 사용
- 프로젝트 특성상 한국어 주석 허용
- 사용자에게 보이는 메시지는 한국어
- 코드 주석도 한국어 사용 가능

```php
// 유효성 체크
// 기념관을 등록합니다.
// 프로필 이미지 업로드
```
