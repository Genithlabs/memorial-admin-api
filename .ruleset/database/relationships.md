# 데이터베이스 관계 규칙

## 기본 원칙

### 관계 정의
- Eloquent 관계 메서드 사용
- 명확하고 의미 있는 메서드명
- 조건이 있는 경우 where 절 추가

## hasOne 관계

### 1:1 관계
- 한 모델이 다른 모델의 하나의 인스턴스를 가짐

```php
public function attachmentProfileImage()
{
    return $this->hasOne(Attachment::class, 'id', 'profile_attachment_id')
        ->where('is_delete', 0);
}

public function attachmentBgm()
{
    return $this->hasOne(Attachment::class, 'id', 'bgm_attachment_id')
        ->where('is_delete', 0);
}
```

### 관계 파라미터
- 첫 번째: 관련 모델 클래스
- 두 번째: 외래키 (관련 모델의 컬럼)
- 세 번째: 로컬키 (현재 모델의 컬럼)

## hasMany 관계

### 1:N 관계
- 한 모델이 다른 모델의 여러 인스턴스를 가짐

```php
public function story()
{
    return $this->hasMany(Story::class, 'memorial_id', 'id')
        ->where('is_visible', 1);
}

public function visitComments()
{
    return $this->hasMany(VisitorComment::class, 'memorial_id', 'id')
        ->where('is_visible', 1);
}
```

### 조건 추가
- where 절로 필터링
- orderBy로 정렬

```php
public function visitComments()
{
    return $this->hasMany(VisitorComment::class, 'memorial_id', 'id')
        ->where('is_visible', 1)
        ->orderBy('created_at', 'desc');
}
```

## belongsTo 관계

### N:1 관계
- 현재 모델이 다른 모델에 속함

```php
public function user()
{
    return $this->belongsTo(User::class, 'user_id', 'id');
}

public function memorial()
{
    return $this->belongsTo(Memorial::class, 'memorial_id', 'id');
}
```

## 복잡한 관계

### Join 포함 관계
- join을 포함한 복잡한 관계

```php
public function visitComments()
{
    return $this->hasMany(VisitorComment::class, 'memorial_id', 'id')
        ->join('mm_users as user', 'mm_visitor_comments.user_id', 'user.id')
        ->select('mm_visitor_comments.id', 
                 'mm_visitor_comments.user_id', 
                 'user.user_name', 
                 'mm_visitor_comments.memorial_id', 
                 'mm_visitor_comments.message', 
                 'mm_visitor_comments.is_visible', 
                 'mm_visitor_comments.created_at', 
                 'mm_visitor_comments.updated_at')
        ->where('mm_visitor_comments.is_visible', 1)
        ->orderBy('mm_visitor_comments.created_at', 'desc');
}
```

### 주의사항
- select로 필요한 컬럼만 선택
- 테이블 별칭 사용
- 컬럼명 충돌 방지

## Eager Loading

### with() 사용
- N+1 쿼리 문제 방지
- 필요한 관계를 미리 로드

```php
$memorial = Memorial::with(['attachmentProfileImage', 'attachmentBgm', 'story'])
    ->where('id', $id)
    ->first();
```

### 중첩 관계
```php
$memorial = Memorial::with([
    'attachmentProfileImage',
    'attachmentBgm',
    'story.attachment'
])->where('id', $id)->first();
```

## 관계 사용

### 직접 접근
```php
$memorial = Memorial::find($id);
$profileImage = $memorial->attachmentProfileImage;
$stories = $memorial->story;
```

### 쿼리 빌더
```php
$memorial = Memorial::with(['attachmentProfileImage', 'attachmentBgm'])
    ->join('mm_users as user', 'mm_memorials.user_id', 'user.id')
    ->select('mm_memorials.id', 'mm_memorials.user_id', 
             'mm_memorials.name', 'user.user_name')
    ->where('mm_memorials.id', $id)
    ->first();
```

## 관계 메서드명 규칙

### 네이밍
- 명확하고 의미 있는 이름
- 관계 타입을 나타내는 이름

```php
attachmentProfileImage()  // 프로필 이미지 첨부파일
attachmentBgm()           // BGM 첨부파일
story()                   // 스토리 목록
visitComments()           // 방문 댓글 목록
user()                    // 사용자
memorial()                // 기념관
```

## 조건부 관계

### where 절
- 삭제되지 않은 것만
- 노출된 것만

```php
->where('is_delete', 0)
->where('is_visible', 1)
```

### 동적 조건
- 파라미터로 조건 전달

```php
public function activeStories()
{
    return $this->hasMany(Story::class, 'memorial_id', 'id')
        ->where('is_visible', 1);
}
```

## 관계 카운트

### withCount()
```php
$memorials = Memorial::withCount('story')->get();
// $memorial->story_count
```

## 관계 존재 확인

### has()
```php
$memorials = Memorial::has('story')->get();
```

### whereHas()
```php
$memorials = Memorial::whereHas('story', function($query) {
    $query->where('is_visible', 1);
})->get();
```
