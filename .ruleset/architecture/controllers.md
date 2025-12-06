# 컨트롤러 아키텍처

## 기본 원칙

### 책임 분리
- 컨트롤러는 HTTP 요청/응답 처리에만 집중
- 비즈니스 로직은 서비스 클래스로 분리
- 데이터 검증은 Validator 사용

### 구조
```php
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ExampleController extends Controller
{
    // 의존성 주입
    protected $service;

    public function __construct(ExampleService $service)
    {
        $this->service = $service;
    }

    // 메서드 구조
    public function action(Request $request)
    {
        // 1. 유효성 검증
        // 2. 권한 확인 (필요시)
        // 3. 비즈니스 로직 처리
        // 4. 응답 반환
    }
}
```

## 유효성 검증

### Validator 사용
- `Validator::make()` 또는 `validator()` 헬퍼 사용
- 커스텀 에러 메시지 제공 (한국어)
- 실패 시 즉시 응답 반환

```php
$validator = Validator::make($request->all(), [
    'user_name' => 'required|max:50',
    'birth_start' => 'required|date_format:Y-m-d',
    'profile' => 'required|mimes:jpeg,jpg,png|max:10240',
], [
    'user_name.required' => '기념인 이름을 입력해 주세요',
    'user_name.max' => '기념인 이름은 50자 이내로 입력해 주세요',
    'birth_start.required' => '기념인 태어난 생년월일을 입력해 주세요',
]);

if ($validator->fails()) {
    return response()->json([
        'result' => 'fail',
        'message' => $validator->errors()->all()
    ], Response::HTTP_BAD_REQUEST);
}
```

### 입력 데이터 추출
- `request()->only()` 사용하여 필요한 필드만 추출
- 보안을 위해 화이트리스트 방식 사용

```php
$data = request()->only('user_name', 'birth_start', 'birth_end', 'career');
```

## 권한 확인

### 인증 확인
- `Auth::user()` 또는 `$request->user()` 사용
- 미들웨어로 인증 처리

```php
$userId = Auth::user()->id;
```

### 리소스 소유권 확인
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

## 트랜잭션 처리

### 데이터베이스 트랜잭션
- 여러 데이터베이스 작업이 있을 경우 트랜잭션 사용
- 예외 발생 시 롤백 처리

```php
try {
    DB::beginTransaction();

    // 데이터베이스 작업들
    $memorial = new Memorial();
    $memorial->save();

    $attachment = new Attachment();
    $attachment->save();

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

## 파일 업로드

### 파일 처리
- `$request->file()` 사용
- 파일 확장자, 크기 검증
- Cloudinary를 통한 업로드

```php
$profile_url = $request->file('profile');
$file = $request->file('profile')->getClientOriginalName();
$extension = pathinfo($file, PATHINFO_EXTENSION);
$lowerExtension = strtolower($extension);
```

## 응답 형식

### 성공 응답
```php
return response()->json([
    'result' => 'success',
    'message' => '처리가 완료되었습니다.',
    'data' => [
        'id' => $memorial->id
    ]
]);
```

### 실패 응답
```php
return response()->json([
    'result' => 'fail',
    'message' => '처리에 실패하였습니다.'
], Response::HTTP_BAD_REQUEST);
```

### HTTP 상태 코드
- `200`: 성공
- `400`: 잘못된 요청 (유효성 검증 실패)
- `401`: 인증 실패
- `404`: 리소스 없음
- `500`: 서버 오류
