# PHP 코딩 표준

## 기본 규칙

### PHP 버전
- PHP 8.0.2 이상 사용
- 타입 힌팅을 적극 활용
- 최신 PHP 기능 활용 권장

### 코드 스타일
- PSR-12 코딩 표준 준수
- 들여쓰기는 4개의 스페이스 사용
- 줄 끝 공백 제거
- 파일 끝에는 빈 줄 하나 추가

### 네임스페이스
- 모든 클래스는 적절한 네임스페이스에 위치
- `App\` 네임스페이스 사용

### 클래스 구조
```php
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    // Properties
    private $property;

    // Constructor
    public function __construct()
    {
        // 초기화 로직
    }

    // Public methods
    public function publicMethod()
    {
        // 구현
    }

    // Protected methods
    protected function protectedMethod()
    {
        // 구현
    }

    // Private methods
    private function privateMethod()
    {
        // 구현
    }
}
```

### 타입 힌팅
- 함수/메서드 파라미터에 타입 힌팅 사용
- 반환 타입 명시 권장
- 가능한 경우 엄격한 타입 체크 사용

```php
public function example(string $param): array
{
    return [];
}
```

### 변수 선언
- 의미 있는 변수명 사용
- 한 줄에 하나의 변수만 선언
- 변수 초기화는 사용 직전에 수행

### 주석
- 복잡한 로직에는 주석 추가
- PHPDoc 주석 사용 권장
- 한국어 주석 허용 (프로젝트 특성상)

```php
/**
 * 기념관을 등록합니다.
 *
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */
public function register(Request $request)
{
    // 구현
}
```
