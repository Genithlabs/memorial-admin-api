# 응답 형식 규칙

## 기본 원칙

### 일관성
- 모든 API 응답은 일관된 구조 사용
- 성공/실패 모두 동일한 형식

## 표준 응답 구조

### 성공 응답
```json
{
    "result": "success",
    "message": "처리가 완료되었습니다.",
    "data": {
        "id": 1
    }
}
```

### 실패 응답
```json
{
    "result": "fail",
    "message": "처리에 실패하였습니다."
}
```

### 에러 응답
```json
{
    "result": "fail",
    "message": ["에러 메시지 1", "에러 메시지 2"]
}
```

## 응답 필드

### result
- `"success"`: 성공
- `"fail"`: 실패

### message
- 문자열: 단일 메시지
- 배열: 여러 에러 메시지 (검증 실패 시)

### data
- 성공 시 반환할 데이터
- 객체 또는 배열

## 성공 응답 예시

### 단순 성공
```php
return response()->json([
    'result' => 'success',
    'message' => '기념관 생성에 성공하였습니다.'
]);
```

### 데이터 포함
```php
return response()->json([
    'result' => 'success',
    'message' => '기념관 생성에 성공하였습니다.',
    'data' => [
        'id' => $memorial->id
    ]
]);
```

### 목록 조회
```php
return response()->json([
    'result' => 'success',
    'message' => '최근 등록된 12개의 기념관 조회가 성공하였습니다.',
    'data' => $memorial
]);
```

## 실패 응답 예시

### 단순 실패
```php
return response()->json([
    'result' => 'fail',
    'message' => '기념관 생성에 실패하였습니다.'
]);
```

### 검증 실패
```php
return response()->json([
    'result' => 'fail',
    'message' => $validator->errors()->all()
], Response::HTTP_BAD_REQUEST);
```

### 예외 포함
```php
return response()->json([
    'result' => 'fail',
    'message' => '기념관 생성에 실패하였습니다. ['.$e->getMessage().']'
]);
```

## 특수 응답

### 토큰 응답
- Passport 토큰 응답은 표준 형식과 다를 수 있음
- 추가 데이터 포함 가능

```php
$tokenData = json_decode((string) $response->getBody(), true);

$additionalData = [
    'is_purchase_request' => $isPurchaseRequest,
    'id' => $user->id,
    'user_id' => $user->user_id,
    'user_name' => $user->user_name,
    'email' => $user->email
];

return response()->json(array_merge($tokenData, $additionalData));
```

### 파일 업로드 응답
```php
return response()->json([
    'result' => 'success',
    'message' => '업로드가 성공하였습니다.',
    'url' => $responseUrl
]);
```

## HTTP 상태 코드

### 상태 코드 사용
- 성공: 기본 200
- 실패: 적절한 상태 코드 사용

```php
// 200 OK (기본)
return response()->json([...]);

// 400 Bad Request
return response()->json([...], Response::HTTP_BAD_REQUEST);

// 401 Unauthorized
return response()->json([...], Response::HTTP_UNAUTHORIZED);
```

### 상태 코드 규칙
- `200`: 성공
- `400`: 잘못된 요청 (유효성 검증 실패)
- `401`: 인증 실패
- `404`: 리소스 없음
- `500`: 서버 오류

## 메시지 작성 규칙

### 한국어 사용
- 모든 메시지는 한국어로 작성
- 사용자 친화적인 메시지

### 명확성
- 무엇이 실패했는지 명확히
- 어떻게 해결할 수 있는지 안내 (가능한 경우)

### 예시
```php
'기념관 생성에 성공하였습니다.'
'기념관 생성에 실패하였습니다.'
'이미 생성된 기념관이 존재합니다.'
'존재하지 않는 기념관입니다.'
'기념관 ID가 없습니다.'
'권한이 없습니다.'
```

## 에러 메시지 배열

### 검증 실패
- `$validator->errors()->all()` 사용
- 모든 검증 에러를 배열로 반환

```php
return response()->json([
    'result' => 'fail',
    'message' => $validator->errors()->all()
], Response::HTTP_BAD_REQUEST);
```

### 단일 메시지
- 단일 에러는 문자열로 반환

```php
return response()->json([
    'result' => 'fail',
    'message' => '기념관 ID가 없습니다.'
]);
```

## 데이터 형식

### 객체
```php
'data' => [
    'id' => $memorial->id,
    'name' => $memorial->name
]
```

### 배열
```php
'data' => $memorials
```

### 관계 포함
- Eloquent 모델의 관계도 포함 가능

```php
$memorial = Memorial::with(['attachmentProfileImage', 'attachmentBgm'])
    ->where('id', $id)
    ->first();

return response()->json([
    'result' => 'success',
    'message' => '기념관 조회가 성공하였습니다.',
    'data' => $memorial
]);
```

## 일관성 유지

### 모든 엔드포인트
- 모든 API 엔드포인트에서 동일한 형식 사용
- 예외 없이 일관된 구조 유지

### 예외 처리
- 예외 발생 시에도 동일한 형식 사용
- 에러 메시지만 변경
