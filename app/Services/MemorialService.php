<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\Memorial;
use Cloudinary\Cloudinary;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MemorialService
{
    private $S3_PATH_PROFILE;
    private $S3_PATH_BGM;
    private $S3_URL;

    protected $cloudinary;
    protected $autoBiographyService;

    public function __construct(Cloudinary $cloudinary, AutoBiographyService $autoBiographyService)
    {
        $this->S3_PATH_PROFILE = "/memorial/profile/";
        $this->S3_PATH_BGM = "/memorial/bgm/";
        $this->S3_URL = "https://foot-print-resources.s3.ap-northeast-2.amazonaws.com";

        $this->cloudinary = $cloudinary;
        $this->autoBiographyService = $autoBiographyService;
    }

    /**
     * 기념관을 생성합니다.
     *
     * @param array $data 기념관 데이터
     *   - user_name: 기념인 이름 (필수)
     *   - birth_start: 태어난 날짜 (필수, Y-m-d 형식)
     *   - birth_end: 마감한 날짜 (선택, Y-m-d 형식)
     *   - career: 생애 내용 (선택, 직접 입력된 경우)
     *   - prompts: 사용자 입력 프롬프트 (선택, AI 생성 시 사용)
     *   - use_ai: AI 생성 사용 여부 (선택, 기본값: false)
     *   - profile: 프로필 이미지 파일 (필수)
     *   - bgm: 배경 음악 파일 (선택)
     * @param int $userId 사용자 ID
     * @return array ['success' => bool, 'memorial' => Memorial|null, 'message' => string]
     */
    public function createMemorial(array $data, int $userId): array
    {
        try {
            DB::beginTransaction();

            // AI 생성 로직: prompts가 있고 use_ai가 true이거나 career가 없으면 AI로 생성
            $careerContent = null;
            if (isset($data['use_ai']) && $data['use_ai'] && isset($data['prompts']) && !empty($data['prompts'])) {
                // AI로 기념관 내용 생성
                $careerContent = $this->generateMemorialContent(
                    $data['user_name'],
                    $data['birth_start'],
                    $data['prompts']
                );
            } elseif (isset($data['career']) && !empty($data['career'])) {
                // 직접 입력된 career 사용
                $careerContent = $data['career'];
            } elseif (isset($data['prompts']) && !empty($data['prompts'])) {
                // prompts만 있고 career가 없으면 AI로 생성 (기본 동작)
                $careerContent = $this->generateMemorialContent(
                    $data['user_name'],
                    $data['birth_start'],
                    $data['prompts']
                );
            }

            $memorial = new Memorial();
            $memorial->user_id = $userId;
            $memorial->name = $data['user_name'];
            $memorial->birth_start = $data['birth_start'];
            if (isset($data['birth_end'])) {
                $memorial->birth_end = $data['birth_end'];
            }
            if ($careerContent) {
                $memorial->career_contents = $careerContent;
            }
            $memorial->save();

            // 프로필 이미지 업로드
            if (isset($data['profile']) && $data['profile']) {
                $profile_url = $data['profile'];
                $file = $profile_url->getClientOriginalName();
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $lowerExtension = strtolower($extension);
                $fileName = $memorial->id."_profile";

                $profileUploadResponse = $this->cloudinary->uploadApi()->upload($profile_url->getRealPath(), $options = [
                    'public_id' => $fileName,
                    'asset_folder' => $this->S3_PATH_PROFILE,
                    'resource_type' => "image",
                    'use_filename' => true,
                ]);

                $publicId = $profileUploadResponse['public_id'];
                $version = $profileUploadResponse['version'];

                $filePath = "/".env('CLOUDINARY_NAME')."/image/upload/v".$version."/";
                $fileName = $publicId.".".$lowerExtension;

                $profileAttachment = new Attachment();
                $profileAttachment->file_path = $filePath;
                $profileAttachment->file_name = $fileName;
                $profileAttachment->save();

                $memorial->profile_attachment_id = $profileAttachment->id;
                $memorial->save();
            }

            // BGM 업로드
            if (isset($data['bgm']) && $data['bgm']) {
                $bgm_url = $data['bgm'];
                $file = $bgm_url->getClientOriginalName();
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $lowerExtension = strtolower($extension);
                $fileName = $memorial->id."_bgm";

                $bgmUploadResponse = $this->cloudinary->uploadApi()->upload($bgm_url->getRealPath(), $options = [
                    'public_id' => $fileName,
                    'asset_folder' => $this->S3_PATH_BGM,
                    'resource_type' => "video",
                    'use_filename' => true,
                ]);

                $publicId = $bgmUploadResponse['public_id'];
                $version = $bgmUploadResponse['version'];

                $filePath = "/".env('CLOUDINARY_NAME')."/video/upload/v".$version."/";
                $fileName = $publicId.".".$lowerExtension;

                $bgmAttachment = new Attachment();
                $bgmAttachment->file_path = $filePath;
                $bgmAttachment->file_name = $fileName;
                $bgmAttachment->save();

                $memorial->bgm_attachment_id = $bgmAttachment->id;
                $memorial->save();
            }

            DB::commit();

            return [
                'success' => true,
                'memorial' => $memorial,
                'message' => '기념관 생성에 성공하였습니다.'
            ];
        } catch (Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'memorial' => null,
                'message' => '기념관 생성에 실패하였습니다. ['.$e->getMessage().']'
            ];
        }
    }

    /**
     * 사용자가 이미 기념관을 생성했는지 확인합니다.
     *
     * @param int $userId 사용자 ID
     * @return Memorial|null
     */
    public function checkExistingMemorial(int $userId): ?Memorial
    {
        return Memorial::where('user_id', $userId)->first();
    }

    /**
     * AI를 사용하여 기념관 내용을 자동 생성합니다.
     *
     * @param string $name 기념인 이름
     * @param string $birthStart 태어난 날짜 (Y-m-d 형식)
     * @param string $prompts 사용자가 입력한 프롬프트/답변
     * @return string 생성된 기념관 내용
     */
    public function generateMemorialContent(string $name, string $birthStart, string $prompts): string
    {
        $prompt = $this->buildMemorialPrompt($name, $birthStart, $prompts);
        
        try {
            $generatedContent = $this->autoBiographyService->generateFromPrompt($prompt);
            return $generatedContent;
        } catch (Exception $e) {
            // AI 생성 실패 시 원본 프롬프트 반환
            return $prompts;
        }
    }

    /**
     * 기념관 내용 생성을 위한 프롬프트를 구성합니다.
     *
     * @param string $name 기념인 이름
     * @param string $birthStart 태어난 날짜
     * @param string $prompts 사용자 입력 프롬프트
     * @return string 구성된 프롬프트
     */
    private function buildMemorialPrompt(string $name, string $birthStart, string $prompts): string
    {
        return <<<EOT
당신은 기념관에 전시될 생애 이야기를 작성하는 전문 작가입니다.
다음 정보를 바탕으로 감성적이고 정제된 기념관 내용을 작성해 주세요.

기본 정보:
- 이름: {$name}
- 생년월일: {$birthStart}

사용자가 제공한 질문과 답변:
{$prompts}

분석 지침:
- 위 내용은 "질문 답변" 형식으로 구성되어 있으므로, 각 질문에 대한 답변을 정확히 파악할 것
- 질문 내용은 생성 결과에 포함하지 말고, 답변 내용만을 바탕으로 자연스러운 생애 이야기를 구성할 것
- 짧은 답변이라도 그 의미를 깊이 해석하고, 시대적 배경이나 감정적 맥락을 상상하여 풍성하게 확장할 것

문단 구성 가이드 (5개 문단):
1. 도입: 탄생과 어린 시절 배경, 성장 환경을 서정적으로 묘사
2. 성장: 어린 시절의 소중한 기억과 인생에 영향을 준 사람에 대한 이야기
3. 성취: 인생에서 가장 자랑스러운 순간과 그 의미
4. 시련과 극복: 힘든 도전을 이겨낸 과정과 그로부터 얻은 교훈
5. 메시지: 다음 세대를 위한 조언과 사랑하는 이들에게 남기는 말

문체 요구사항:
- 기념관에 적합한 존중과 감동이 담긴 문체로 작성
- 계절, 풍경, 시대적 분위기 등 배경 묘사를 활용하여 생동감 있게 표현
- 비유나 은유를 적절히 사용하여 문학적 깊이를 더할 것
- 과도한 감정 표현보다는 사실과 감성을 균형있게 표현

분량:
- 1500자 이상 3000자 이내로 충분히 풍성하게 작성할 것
- 각 문단은 최소 200자 이상으로 구성

위 내용을 바탕으로 기념관에 전시할 생애 이야기를 작성해 주세요.
EOT;
    }
}
