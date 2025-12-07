# 마이그레이션 규칙

## 기본 구조

### 마이그레이션 파일
- Laravel 마이그레이션 네이밍 규칙 준수
- `YYYY_MM_DD_HHMMSS_action_description.php` 형식

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mm_examples', function (Blueprint $table) {
            // 테이블 정의
        });
    }

    public function down()
    {
        Schema::dropIfExists('mm_examples');
    }
};
```

## 테이블 생성

### 기본 설정
- 엔진: InnoDB
- 문자셋: utf8mb4
- 타임스탬프: 기본 사용

```php
Schema::create('mm_examples', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->charset = 'utf8mb4';
    $table->bigIncrements('id')->unique();
    // 컬럼 정의
    $table->timestamps();
});
```

### 기본키
- `bigIncrements('id')` 사용
- `unique()` 제약 추가

```php
$table->bigIncrements('id')->unique();
```

## 컬럼 타입

### 정수
```php
$table->unsignedBigInteger('user_id')->default(0)->comment('회원 고유키');
$table->unsignedTinyInteger('is_visible')->default(1)->comment('노출 여부(0:false, 1:true)');
```

### 문자열
```php
$table->string('user_name', 50);
$table->string('email', 100);
```

### 텍스트
```php
$table->text('message')->nullable()->comment('메세지');
$table->text('career_contents')->nullable();
```

### 날짜
```php
$table->date('birth_start');
$table->date('birth_end')->nullable();
```

### 타임스탬프
```php
$table->timestamps(); // created_at, updated_at
```

## 제약 조건

### 기본값
```php
$table->unsignedBigInteger('user_id')->default(0);
$table->unsignedTinyInteger('is_visible')->default(1);
```

### NULL 허용
```php
$table->text('message')->nullable();
$table->date('birth_end')->nullable();
```

### 코멘트
- 모든 컬럼에 한국어 코멘트 추가

```php
$table->unsignedBigInteger('user_id')->default(0)->comment('회원 고유키');
$table->unsignedTinyInteger('is_visible')->default(1)->comment('노출 여부(0:false, 1:true)');
```

## 외래키

### 외래키 정의
- `foreign()` 메서드 사용
- `cascadeOnDelete()` 또는 `restrictOnDelete()` 설정

```php
$table->foreign('user_id')->references('id')->on('mm_users')->cascadeOnDelete();
$table->foreign('memorial_id')->references('id')->on('mm_memorials')->cascadeOnDelete();
```

### 인덱스
- 외래키는 자동으로 인덱스 생성
- 추가 인덱스 필요 시 명시

```php
$table->index('user_id');
$table->index(['memorial_id', 'is_visible']);
```

## 테이블 수정

### 컬럼 추가
- 별도 마이그레이션 파일로 생성

```php
Schema::table('mm_users', function (Blueprint $table) {
    $table->string('user_phone', 255)->nullable()->after('email');
});
```

### 컬럼 수정
```php
Schema::table('mm_users', function (Blueprint $table) {
    $table->string('user_name', 100)->change();
});
```

### 컬럼 삭제
```php
Schema::table('mm_users', function (Blueprint $table) {
    $table->dropColumn('old_column');
});
```

## 테이블명 규칙

### 접두사
- 모든 테이블은 `mm_` 접두사 사용

```
mm_users
mm_memorials
mm_stories
mm_attachments
mm_visitor_comments
mm_purchase_requests
```

### 네이밍
- 복수형 사용
- snake_case 사용

## 롤백

### down() 메서드
- 모든 마이그레이션은 `down()` 메서드 구현
- 테이블 삭제 또는 변경 사항 되돌리기

```php
public function down()
{
    Schema::dropIfExists('mm_examples');
}
```

## 마이그레이션 실행

### 실행 순서
- 타임스탬프에 따라 자동 실행
- 외래키 의존성 고려

### 롤백
```bash
php artisan migrate:rollback
php artisan migrate:rollback --step=1
```

## 예시

### 완전한 예시
```php
Schema::create('mm_stories', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->charset = 'utf8mb4';
    $table->bigIncrements('id')->unique();
    $table->unsignedBigInteger('user_id')->default(0)->comment('회원 고유키');
    $table->unsignedBigInteger('memorial_id')->default(0)->comment('기념관 고유키');
    $table->text('message')->nullable()->comment('메세지');
    $table->unsignedBigInteger('attachment_id')->nullable()->comment('첨부 파일 고유키');
    $table->unsignedTinyInteger('is_visible')->default(1)->comment('노출 여부(0:false, 1:true)');
    $table->timestamps();
    $table->foreign('user_id')->references('id')->on('mm_users')->cascadeOnDelete();
    $table->foreign('memorial_id')->references('id')->on('mm_memorials')->cascadeOnDelete();
});
```
