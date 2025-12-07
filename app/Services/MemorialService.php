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

    public function __construct(Cloudinary $cloudinary)
    {
        $this->S3_PATH_PROFILE = "/memorial/profile/";
        $this->S3_PATH_BGM = "/memorial/bgm/";
        $this->S3_URL = "https://foot-print-resources.s3.ap-northeast-2.amazonaws.com";

        $this->cloudinary = $cloudinary;
    }

    /**
     * 기념관을 생성합니다.
     *
     * @param array $data 기념관 데이터
     *   - user_name: 기념인 이름 (필수)
     *   - birth_start: 태어난 날짜 (필수, Y-m-d 형식)
     *   - birth_end: 마감한 날짜 (선택, Y-m-d 형식)
     *   - career: 생애 내용 (선택)
     *   - profile: 프로필 이미지 파일 (필수)
     *   - bgm: 배경 음악 파일 (선택)
     * @param int $userId 사용자 ID
     * @return array ['success' => bool, 'memorial' => Memorial|null, 'message' => string]
     */
    public function createMemorial(array $data, int $userId): array
    {
        try {
            DB::beginTransaction();

            $memorial = new Memorial();
            $memorial->user_id = $userId;
            $memorial->name = $data['user_name'];
            $memorial->birth_start = $data['birth_start'];
            if (isset($data['birth_end'])) {
                $memorial->birth_end = $data['birth_end'];
            }
            if (isset($data['career'])) {
                $memorial->career_contents = $data['career'];
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
}
