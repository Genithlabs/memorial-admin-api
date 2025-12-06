# 검증 규칙

## 기본 원칙

### 입력 검증
- 모든 사용자 입력은 검증 필수
- 서버 측 검증 필수 (클라이언트 검증은 보완용)
- 검증 실패 시 즉시 응답 반환

## Validator 사용

### 기본 구조
- `Validator::make()` 또는 `validator()` 헬퍼 사용
- 규칙과 커스텀 메시지 제공

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
```

### 검증 실패 처리
- 실패 시 에러 메시지 배열 반환
- HTTP 400 상태 코드 사용

```php
if ($validator->fails()) {
    return response()->json([
        'result' => 'fail',
        'message' => $validator->errors()->all()
    ], Response::HTTP_BAD_REQUEST);
}
```

## 일반적인 검증 규칙

### 문자열
```php
'user_name' => 'required|string|max:50',
'email' => 'required|string|email|max:100|unique:mm_users',
'user_id' => 'required|string|max:50|unique:mm_users',
```

### 숫자
```php
'id' => 'required|integer|exists:mm_memorials,id',
'user_id' => 'required|integer',
```

### 날짜
```php
'birth_start' => 'required|date_format:Y-m-d',
'birth_end' => 'sometimes|date_format:Y-m-d',
```

### 파일
```php
'profile' => 'required|mimes:jpeg,jpg,png|max:10240',
'bgm' => 'sometimes|mimes:mp3,mp4,mpa,m4a|max:10240',
'career_contents_file' => 'required|mimes:jpeg,jpg,png|max:10240',
```

### 비밀번호
```php
'user_password' => 'required|string|min:6|max:255',
'password' => 'required|confirmed',
```

## 조건부 검증

### sometimes 규칙
- 필드가 존재할 때만 검증

```php
'profile' => 'sometimes|mimes:jpeg,jpg,png|max:10240',
'bgm' => 'sometimes|mimes:mp3,mp4,mpa,m4a|max:10240',
```

### 조건부 필수
- 특정 조건에서만 필수

```php
'birth_end' => 'required_if:birth_start,!=,null|date_format:Y-m-d',
```

## 고유성 검증

### 데이터베이스 고유성
```php
'user_id' => 'required|string|max:50|unique:mm_users',
'email' => 'required|string|email|max:100|unique:mm_users',
```

### 업데이트 시 고유성
```php
'email' => 'required|string|email|max:100|unique:mm_users,email,' . $userId,
```

## 파일 검증

### 파일 타입
- MIME 타입 검증
- 확장자 검증

```php
'profile' => 'required|mimes:jpeg,jpg,png|max:10240',
```

### 파일 크기
- 최대 크기 제한 (KB 단위)
- 10MB = 10240KB

```php
'profile' => 'max:10240', // 10MB
```

### 허용 파일 타입
- 이미지: `jpeg`, `jpg`, `png`
- 오디오/비디오: `mp3`, `mp4`, `mpa`, `m4a`

## 커스텀 검증 메시지

### 한국어 메시지
- 모든 검증 메시지는 한국어로 제공
- 사용자 친화적인 메시지 작성

```php
[
    'user_name.required' => '기념인 이름을 입력해 주세요',
    'user_name.max' => '기념인 이름은 50자 이내로 입력해 주세요',
    'profile.required' => '기념인 프로필 사진을 선택해 주세요',
    'profile.mimes' => '기념인 프로필 사진은 jpg/jpeg/png 형식이여야 합니다',
    'profile.max' => '기념인 프로필 사진은 10Mb 이하여야 합니다',
]
```

## 수동 검증

### 추가 검증
- Validator로 처리하기 어려운 경우 수동 검증

```php
if (is_null($memorialId)) {
    return response()->json([
        'result' => 'fail',
        'message' => '기념관 ID가 없습니다.'
    ]);
}

$memorial = Memorial::where('id', $id)->first();
if (is_null($memorial)) {
    return response()->json([
        'result' => 'fail',
        'message' => '존재하지 않는 기념관입니다.'
    ]);
}
```

## 입력 데이터 정제

### only() 사용
- 필요한 필드만 추출
- 화이트리스트 방식으로 보안 강화

```php
$data = request()->only('user_name', 'birth_start', 'birth_end', 'career');
```

### 입력 정제
- XSS 방지를 위한 입력 정제
- HTML 태그 제거 (필요시)

```php
$data['user_name'] = strip_tags($data['user_name']);
```
