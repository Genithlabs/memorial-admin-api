# Memorial Admin API (발자취)

고인을 기리는 추모 기념관 웹사이트의 백엔드 API 서버입니다.
기념관 생성, AI 기반 자서전 자동 생성, 스토리 관리, 방문자 댓글 등의 기능을 제공합니다.

## 기술 스택

- **Framework**: Laravel 9
- **Language**: PHP 8.1
- **Database**: MariaDB (utf8mb4)
- **Authentication**: Laravel Passport (OAuth 2.0)
- **AI**: Google Gemini 2.5 Flash-Lite API
- **Media Storage**: Cloudinary CDN
- **Email**: AWS SES
- **Infra**: Docker (Ubuntu 20.04 + Nginx + PHP-FPM + Supervisor)

## 주요 기능

### 기념관 관리
- 기념관 생성 / 수정 / 조회
- 프로필 이미지, BGM 업로드 (Cloudinary)
- 공개 / 비공개 설정

### AI 콘텐츠 생성
- Google Gemini API를 활용한 추모 자서전 자동 생성
- Chat 플로우를 통한 대화형 기념관 생성

### 스토리
- 기념관에 생애 이야기 등록
- 이미지, 동영상, 오디오 첨부 지원

### 방문자 댓글
- 기념관 방문자 댓글 작성 / 조회

### 사용자 인증
- 회원가입 / 로그인 (Passport OAuth 2.0)
- 비밀번호 찾기 / 재설정 (AWS SES 이메일 발송)

## API 엔드포인트

### 인증 (`/api/user`)

| Method | Endpoint | 설명 | 인증 |
|--------|----------|------|------|
| POST | `/user/register` | 회원가입 | - |
| POST | `/user/login` | 로그인 | - |
| POST | `/user/findId` | 아이디 찾기 | - |
| POST | `/user/forgot_password` | 비밀번호 찾기 | - |
| POST | `/user/reset_password` | 비밀번호 재설정 | - |
| POST | `/user/request_purchase` | 구매 요청 | O |

### Chat 플로우 (`/api/chat`)

| Method | Endpoint | 설명 | 인증 |
|--------|----------|------|------|
| GET | `/chat/questions` | 질문 목록 조회 | - |
| POST | `/chat/submit` | 응답 제출 및 기념관 생성 | O |

### 기념관 (`/api/memorial`)

| Method | Endpoint | 설명 | 인증 |
|--------|----------|------|------|
| POST | `/memorial/register` | 기념관 생성 | O |
| POST | `/memorial/upload` | 콘텐츠 이미지 업로드 | O |
| POST | `/memorial/{id}/edit` | 기념관 수정 | O |
| GET | `/memorial/view` | 내 기념관 조회 | O |
| GET | `/memorial/index` | 공개 기념관 목록 | - |
| GET | `/memorial/{id}/detail` | 기념관 상세 조회 | - |
| POST | `/memorial/{id}/comment/register` | 댓글 등록 | O |
| GET | `/memorial/{id}/comments` | 댓글 목록 조회 | - |
| POST | `/memorial/{id}/story/register` | 스토리 등록 | O |
| GET | `/memorial/{id}/stories` | 스토리 목록 조회 | - |
| POST | `/memorial/{id}/story/delete` | 스토리 삭제 | O |

## 프로젝트 구조

```
app/
├── Http/Controllers/
│   └── API/            # API 컨트롤러
├── Models/             # Eloquent 모델
├── Services/           # 비즈니스 로직
│   ├── MemorialService.php
│   └── AutoBiographyService.php
├── Mail/               # 이메일 템플릿
└── Providers/          # 서비스 프로바이더
config/                 # Laravel 설정 파일
database/
├── migrations/         # DB 마이그레이션
└── seeders/            # 시드 데이터
routes/
├── api.php             # API 라우트
└── web.php             # 웹 라우트
conf/
├── nginx.conf          # Nginx 설정
└── supervisord.conf    # Supervisor 설정
```

## 로컬 개발 환경 설정

### Docker 실행

```bash
docker-compose up -d
```

서비스 구성:
- **nginx_php81**: 웹 서버 (포트 80, 443)
- **mariadb**: 데이터베이스 (포트 3306)

### 환경 설정

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan passport:install
```

### 필수 환경 변수

| 변수 | 설명 |
|------|------|
| `DB_HOST` | DB 호스트 |
| `DB_DATABASE` | DB 이름 (`memorial`) |
| `CLOUDINARY_URL` | Cloudinary 연결 URL |
| `GOOGLE_GEMINI_API_KEY` | Google Gemini API 키 |
| `MAIL_USERNAME` / `MAIL_PASSWORD` | AWS SES SMTP 인증 |

## AI (Google Gemini) 관리

### 모델 정보
- **사용 모델**: Gemini 2.5 Flash-Lite
- **프로젝트 ID**: `gen-lang-client-055*******`
- **프로젝트 번호**: `9520068*****`

### 요금

| 항목 | 가격 (1M 토큰당) |
|------|-----------------|
| Input | $0.10 |
| Output | $0.40 |

### 사용량 및 과금 확인
- **API 사용량 모니터링**: https://console.cloud.google.com/apis/api/generativelanguage.googleapis.com/metrics?project=gen-lang-client-055*******
- **API 키 관리**: https://console.cloud.google.com/apis/credentials?project=gen-lang-client-055*******
- **결제 및 과금 확인**: https://console.cloud.google.com/billing?project=gen-lang-client-055*******

## 파일 업로드 제한

| 유형 | 허용 확장자 | 최대 크기 |
|------|------------|----------|
| 프로필 이미지 | jpeg, jpg, png | 10MB |
| BGM | mp3, mp4, mpa, m4a | 10MB |
| 스토리 첨부 | 이미지, 동영상, 오디오 | - |
