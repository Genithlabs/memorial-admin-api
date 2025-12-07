# 파일 업로드 보안 규칙

## 기본 원칙

### 파일 검증
- 모든 업로드 파일은 타입과 크기 검증 필수
- 서버 측 검증 필수 (클라이언트 검증은 보완용)

## Cloudinary 사용

### 기본 설정
- Cloudinary를 파일 저장소로 사용
- S3 대신 Cloudinary 사용

### 업로드 프로세스
1. 파일 검증 (타입, 크기)
2. Cloudinary에 업로드
3. Attachment 모델에 메타데이터 저장
4. URL 반환

## 파일 검증

### 타입 검증
```php
$validator = Validator::make($request->all(), [
    'profile' => 'required|mimes:jpeg,jpg,png|max:10240',
    'bgm' => 'sometimes|mimes:mp3,mp4,mpa,m4a|max:10240',
], [
    'profile.mimes' => '기념인 프로필 사진은 jpg/jpeg/png 형식이여야 합니다',
    'profile.max' => '기념인 프로필 사진은 10Mb 이하여야 합니다',
]);
```

### 허용 파일 타입
- 이미지: `jpeg`, `jpg`, `png`
- 오디오/비디오: `mp3`, `mp4`, `mpa`, `m4a`

### 파일 크기 제한
- 최대 10MB (10240KB)
- 모든 파일 타입에 동일 적용

## 파일 업로드 구현

### 파일 정보 추출
```php
$profile_url = $request->file('profile');
$file = $request->file('profile')->getClientOriginalName();
$extension = pathinfo($file, PATHINFO_EXTENSION);
$lowerExtension = strtolower($extension);
```

### 파일명 생성
- 고유한 파일명 생성
- 리소스 ID 포함

```php
$fileName = $memorial->id."_profile";
// 또는
$randomString = random_int(1, 10000000);
$fileName = Auth::user()->id."_".$randomString;
```

### Cloudinary 업로드
```php
$profileUploadResponse = $this->cloudinary->uploadApi()->upload(
    $profile_url->getRealPath(), 
    $options = [
        'public_id' => $fileName,
        'asset_folder' => $this->S3_PATH_PROFILE,
        'resource_type' => "image",
        'use_filename' => true,
    ]
);
```

### 리소스 타입
- 이미지: `resource_type => "image"`
- 비디오/오디오: `resource_type => "video"`

## 파일 경로 관리

### 경로 상수
- 클래스 내부에 경로 상수 정의

```php
private $S3_PATH_PROFILE = "/memorial/profile/";
private $S3_PATH_BGM = "/memorial/bgm/";
private $S3_PATH_CAREER_CONTENT_FILE = "/memorial/career/";
private $S3_PATH_STORY_ATTACHMENT = "/memorial/story/";
```

### 경로 구조
- 리소스 타입별로 폴더 분리
- 계층적 구조 유지

## Attachment 모델

### 메타데이터 저장
- 파일 경로와 파일명을 Attachment 모델에 저장
- 다른 모델에서 참조

```php
$profileAttachment = new Attachment();
$profileAttachment->file_path = $filePath;
$profileAttachment->file_name = $fileName;
$profileAttachment->save();

$memorial->profile_attachment_id = $profileAttachment->id;
$memorial->save();
```

### 파일 경로 구성
```php
$publicId = $profileUploadResponse['public_id'];
$version = $profileUploadResponse['version'];

$filePath = "/".env('CLOUDINARY_NAME')."/image/upload/v".$version."/";
$fileName = $publicId.".".$lowerExtension;
```

## URL 생성

### Secure URL
- 환경 변수에 따라 secure URL 사용

```php
if (env('CLOUDINARY_SECURE') == true) {
    $responseUrl = $profileUploadResponse['secure_url'];
} else {
    $responseUrl = $profileUploadResponse['url'];
}
```

### 응답
```php
return response()->json([
    'result' => 'success',
    'message' => '업로드가 성공하였습니다.',
    'url' => $responseUrl
]);
```

## 파일 업데이트

### 기존 파일 삭제
- 파일 업데이트 시 기존 Attachment 삭제

```php
Attachment::where('id', $memorial->profile_attachment_id)->delete();

// 새 파일 업로드
$profileAttachment = new Attachment();
// ...
```

## 보안 고려사항

### 파일명 검증
- 파일명에 특수문자 제한
- 경로 탐색 공격 방지

### 파일 크기 제한
- 서버 설정과 애플리케이션 설정 모두 확인
- `php.ini`의 `upload_max_filesize`, `post_max_size` 확인

### MIME 타입 검증
- 확장자만이 아닌 실제 파일 타입 검증
- Cloudinary가 추가 검증 수행

### 업로드 경로
- 직접 파일 시스템 접근 불가
- Cloudinary를 통해서만 접근

## 에러 처리

### 업로드 실패
```php
try {
    // 업로드 로직
} catch (Exception $e) {
    return response()->json([
        'result' => 'fail',
        'message' => '업로드가 실패하였습니다. ['.$e->getMessage().']'
    ]);
}
```
