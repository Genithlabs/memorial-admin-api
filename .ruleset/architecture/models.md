# 모델 아키텍처

## 기본 구조

### 모델 클래스
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Example extends Model
{
    use HasFactory;

    protected $table = 'mm_examples';
    
    public $timestamps = true;

    protected $fillable = [
        'column1',
        'column2',
    ];

    protected $hidden = [
        'sensitive_data',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
```

## 테이블명

### 커스텀 테이블명
- 모든 테이블은 `mm_` 접두사 사용
- `$table` 속성으로 명시

```php
protected $table = 'mm_memorials';
protected $table = 'mm_users';
protected $table = 'mm_stories';
```

## 관계 정의

### hasOne 관계
- 1:1 관계
- 명확한 메서드명 사용

```php
public function attachmentProfileImage()
{
    return $this->hasOne(Attachment::class, 'id', 'profile_attachment_id')
        ->where('is_delete', 0);
}
```

### hasMany 관계
- 1:N 관계
- 조건이 있는 경우 where 절 추가

```php
public function story()
{
    return $this->hasMany(Story::class, 'memorial_id', 'id')
        ->where('is_visible', 1);
}
```

### belongsTo 관계
- N:1 관계

```php
public function user()
{
    return $this->belongsTo(User::class, 'user_id', 'id');
}
```

### 복잡한 관계
- join을 포함한 관계는 쿼리 빌더 활용

```php
public function visitComments()
{
    return $this->hasMany(VisitorComment::class, 'memorial_id', 'id')
        ->join('mm_users as user', 'mm_visitor_comments.user_id', 'user.id')
        ->select('mm_visitor_comments.id', 'mm_visitor_comments.user_id', 
                  'user.user_name', 'mm_visitor_comments.memorial_id', 
                  'mm_visitor_comments.message', 'mm_visitor_comments.is_visible', 
                  'mm_visitor_comments.created_at', 'mm_visitor_comments.updated_at')
        ->where('mm_visitor_comments.is_visible', 1)
        ->orderBy('mm_visitor_comments.created_at', 'desc');
}
```

## Mass Assignment

### Fillable
- Mass Assignment가 필요한 필드만 `$fillable`에 추가
- 보안을 위해 화이트리스트 방식 사용

```php
protected $fillable = [
    'user_id',
    'user_name',
    'email',
    'user_password',
];
```

### Guarded
- `$guarded` 사용 시 모든 필드를 보호하고 필요한 것만 제외

```php
protected $guarded = [];
```

## Hidden 속성

### 민감한 정보
- API 응답에서 제외할 필드는 `$hidden`에 추가

```php
protected $hidden = [
    'user_password',
    'remember_token',
];
```

## 타입 캐스팅

### Casts
- 날짜, JSON 등 타입 변환이 필요한 필드

```php
protected $casts = [
    'email_verified_at' => 'datetime',
    'is_active' => 'boolean',
    'metadata' => 'array',
];
```

## 커스텀 메서드

### 인증 관련
- Passport 인증을 위한 커스텀 메서드

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

## 쿼리 스코프

### 글로벌 스코프
- 자주 사용되는 조건은 스코프로 정의

```php
public function scopeActive($query)
{
    return $query->where('is_visible', 1);
}
```

### 로컬 스코프
- 특정 조건의 쿼리

```php
public function scopeVisible($query)
{
    return $query->where('is_visible', 1);
}
```

## Eager Loading

### with() 사용
- N+1 쿼리 문제 방지를 위해 관계 미리 로드

```php
$memorial = Memorial::with(['attachmentProfileImage', 'attachmentBgm', 'story'])
    ->where('id', $id)
    ->first();
```

## 조인 쿼리

### join() 사용
- 필요한 경우 join 사용
- 테이블 별칭 사용

```php
$memorial = Memorial::with(['attachmentProfileImage', 'attachmentBgm'])
    ->join('mm_users as user', 'mm_memorials.user_id', 'user.id')
    ->select('mm_memorials.id', 'mm_memorials.user_id', 
             'mm_memorials.name', 'user.user_name')
    ->where('mm_memorials.id', $id)
    ->first();
```
