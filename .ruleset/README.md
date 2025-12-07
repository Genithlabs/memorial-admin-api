# 프로젝트 Ruleset

이 디렉토리는 Memorial Admin API 프로젝트의 개발 규칙과 가이드라인을 포함합니다.

## 구조

```
.ruleset/
├── README.md (이 파일)
├── coding-standards/     # 코딩 표준
│   ├── php.md
│   ├── laravel.md
│   └── naming-conventions.md
├── architecture/        # 아키텍처 패턴
│   ├── controllers.md
│   ├── models.md
│   ├── services.md
│   └── api-design.md
├── security/            # 보안 관련 규칙
│   ├── authentication.md
│   ├── validation.md
│   └── file-upload.md
├── database/            # 데이터베이스 관련
│   ├── migrations.md
│   └── relationships.md
└── error-handling/      # 에러 처리 및 응답
    ├── exceptions.md
    └── response-format.md
```

## 사용 방법

이 ruleset은 AI 코딩 어시스턴트(Cursor, Claude Code 등)가 프로젝트의 코딩 스타일과 패턴을 이해하고 일관된 코드를 생성하도록 도와줍니다.

각 파일은 특정 주제에 대한 규칙과 가이드라인을 포함하며, 프로젝트의 기존 코드 패턴을 기반으로 작성되었습니다.

## 중요 사항

- 이 ruleset은 특정 AI 엔진에 종속되지 않도록 작성되었습니다.
- 모든 규칙은 프로젝트의 기존 코드 패턴을 분석하여 작성되었습니다.
- 새로운 기능을 추가하거나 기존 코드를 수정할 때 이 규칙을 참고하세요.
