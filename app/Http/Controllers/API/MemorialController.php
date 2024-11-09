<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Memorial;
use App\Models\VisitorComment;
use Cloudinary\Cloudinary;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class MemorialController extends Controller
{
    private $S3_PATH_PROFILE;
    private $S3_PATH_BGM;
    private $S3_PATH_CAREER_CONTENT_FILE;
    private $S3_URL;

    protected $cloudinary;

    public function __construct(Cloudinary $cloudinary) {
        $this->S3_PATH_PROFILE = "/memorial/profile/";
        $this->S3_PATH_BGM = "/memorial/bgm/";
        $this->S3_PATH_CAREER_CONTENT_FILE = "/memorial/career/";
        $this->S3_URL = "https://foot-print-resources.s3.ap-northeast-2.amazonaws.com";

        $this->cloudinary = $cloudinary;
    }

    public function register(Request $request) {
        // 유효성 체크
        $memorial = Memorial::where('user_id', Auth::user()->id)->first();
        if (!is_null($memorial)) {
            return response()->json([
                'result' => 'fail',
                'message' => '이미 생성된 기념관이 존재합니다.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'user_name' => 'required|max:50',
            'birth_start' => 'required|sometimes|date_format:Y-m-d',
            'profile' => 'required|mimes:jpeg,jpg,png|max:10240',
            'bgm' => 'sometimes|mimes:mp3,mp4,mpa,m4a|max:102400',
        ], [
            'user_name.required' => '기념인 이름을 입력해 주세요',
            'user_name.max' => '기념인 이름은 50자 이내로 입력해 주세요',
            'birth_start.required' => '기념인 태어난 생년월일을 입력해 주세요',
            'profile.required' => '기념인 프로필 사진을 선택해 주세요',
            'profile.mimes' => '기념인 프로필 사진은 jpg/jpeg/png 형식이여야 합니다',
            'profile.max' => '기념인 프로필 사진은 10Mb 이하여야 합니다',
            'bgm.mimes' => '기념관 배경 음악은 mp3, mp4, mpa, m4a 형식이여야 합니다',
            'bgm.max' => '기념관 배경 음악은 100Mb 이하여야 합니다'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'result' => 'fail',
                'message' => $validator->errors()->all()
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = request()->only('user_name', 'birth_start', 'birth_end', 'career', 'profile', 'bgm');

        try {
            DB::beginTransaction();

            $id = Auth::user()->id;

            $memorial = new Memorial();
            $memorial->user_id = $id;
            $memorial->name = $data['user_name'];
            $memorial->birth_start = $data['birth_start'];
            if (isset($data['birth_end'])) {
                $memorial->birth_end = $data['birth_end'];
            }
            $memorial->career_contents = $data['career'];
            $memorial->save();

            // 프로필 이미지 업로드
            $profile_url = $request->file('profile');
            $file = $request->file('profile')->getClientOriginalName();
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $lowerExtentsion = strtolower($extension);
            $fileName = $memorial->id."_profile";

            $profileUploadResponse = $this->cloudinary->uploadApi()->upload($profile_url->getRealPath(), $options = [
                'public_id' => $fileName,
                'asset_folder' => $this->S3_PATH_PROFILE,
                'resource_type' => "image",
                'use_filename' => true, // 원본 파일명을 public_id 로 사용함
            ]);

            $publicId = $profileUploadResponse['public_id'];
            $version = $profileUploadResponse['version'];

            $filePath = "/".env('CLOUDINARY_NAME')."/image/upload/v".$version."/";
            $fileName = $publicId.".".$lowerExtentsion;

            $profileAttachment = new Attachment();
            $profileAttachment->file_path = $filePath;
            $profileAttachment->file_name = $fileName;
            $profileAttachment->save();

            $memorial->profile_attachment_id = $profileAttachment->id;
            $memorial->save();

            // BGM 업로드
            $bgm_url = $request->file('bgm');
            if ($bgm_url) {
                $file = $request->file('bgm')->getClientOriginalName();
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $lowerExtentsion = strtolower($extension);
                $fileName = $memorial->id."_bgm";

                $bgmUploadResponse = $this->cloudinary->uploadApi()->upload($bgm_url->getRealPath(), $options = [
                    'public_id' => $fileName,
                    'asset_folder' => $this->S3_PATH_BGM,
                    'resource_type' => "video",
                    'use_filename' => true, // 원본 파일명을 public_id 로 사용함
                ]);

                $publicId = $bgmUploadResponse['public_id'];
                $version = $bgmUploadResponse['version'];

                $filePath = "/".env('CLOUDINARY_NAME')."/video/upload/v".$version."/";
                $fileName = $publicId.".".$lowerExtentsion;

                $bgmAttachment = new Attachment();
                $bgmAttachment->file_path = $filePath;
                $bgmAttachment->file_name = $fileName;
                $bgmAttachment->save();

                $memorial->bgm_attachment_id = $bgmAttachment->id;
                $memorial->save();
            }

            DB::commit();

            return response()->json([
                'result' => 'success',
                'message' => '기념관 생성에 성공하였습니다.',
                'data' => [
                    'id' => $memorial->id
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => 'fail',
                'message' => '기념관 생성에 실패하였습니다. ['.$e->getMessage().']'
            ]);
        }
    }

    public function upload(Request $request) {
        $validator = Validator::make($request->all(), [
            'career_contents_file' => 'required|mimes:jpeg,jpg,png|max:10240'
        ], [
            'career_contents_file.required' => '이미지를 선택해 주세요',
            'career_contents_file.mimes' => '이미지는 jpg/jpeg/png 형식이여야 합니다',
            'career_contents_file.max' => '이미지는 10Mb 이하여야 합니다'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'result' => 'fail',
                'message' => $validator->errors()->all()
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = request()->only('career_contents_file');

        try {
            // 생애 컨텐츠에 포함될 파일 업로드
            $upload_file_url = $request->file('career_contents_file');
            $file = $request->file('career_contents_file')->getClientOriginalName();
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $lowerExtentsion = strtolower($extension);
            $randomString = random_int(1, 10000000);
            $fileName = Auth::user()->id."_".$randomString;
            $uploadPathFileName = $this->S3_PATH_CAREER_CONTENT_FILE.$fileName;

            $profileUploadResponse = $this->cloudinary->uploadApi()->upload($upload_file_url->getRealPath(), $options = [
                'public_id' => $fileName,
                'asset_folder' => $this->S3_PATH_CAREER_CONTENT_FILE,
                'resource_type' => "image",
                'use_filename' => true, // 원본 파일명을 public_id 로 사용함
            ]);

            if (env('CLOUDINARY_SECURE') == true) {
                $responseUrl = $profileUploadResponse['secure_url'];
            } else {
                $responseUrl = $profileUploadResponse['url'];
            }

            return response()->json([
                'result' => 'success',
                'message' => '업로드가 성공하였습니다.',
                'url' => $responseUrl
            ]);
        } catch (Exception $e) {
            return response()->json([
                'result' => 'fail',
                'message' => '업로드가 실패하였습니다. ['.$e->getMessage().']'
            ]);
        }
    }

    public function edit(Request $request, $id) {
        // 유효성 체크
        $memorial = Memorial::where('id', $id)->first();
        if (is_null($memorial)) {
            return response()->json([
                'result' => 'fail',
                'message' => '존재하지 않는 기념관입니다.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'user_name' => 'required|max:50',
            'birth_start' => 'required|sometimes|date_format:Y-m-d',
            'profile' => 'sometimes|mimes:jpeg,jpg,png|max:1024',
            'bgm' => 'sometimes|mimes:mp3,mp4,mpa,m4a|max:4096',
        ], [
            'user_name.required' => '기념인 이름을 입력해 주세요',
            'user_name.max' => '기념인 이름은 50자 이내로 입력해 주세요',
            'birth_start.required' => '기념인 태어난 생년월일을 입력해 주세요',
            'profile.required' => '기념인 프로필 사진을 선택해 주세요',
            'profile.mimes' => '기념인 프로필 사진은 jpg/jpeg/png 형식이여야 합니다',
            'profile.max' => '기념인 프로필 사진은 1Mb 이하여야 합니다',
            'bgm.mimes' => '기념관 배경 음악은 mp3, mp4, mpa, m4a 형식이여야 합니다',
            'bgm.max' => '기념관 배경 음악은 4Mb 이하여야 합니다'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'result' => 'fail',
                'message' => $validator->errors()->all()
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = request()->only('user_name', 'birth_start', 'birth_end', 'career', 'profile', 'bgm');

        try {
            DB::beginTransaction();

            $memorial = Memorial::where('id', $id)->first();

            $updateColumn = [
                'name' =>  $data['user_name'],
                'birth_start' => $data['birth_start'],
                'birth_end' => (isset($data['birth_end']) ? $data['birth_end'] : null),
                'career_contents' => $data['career']
            ];

            // 프로필 이미지 업로드
            $profile_url = $request->file('profile');
            if ($profile_url) {
                $file = $request->file('profile')->getClientOriginalName();
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $lowerExtentsion = strtolower($extension);
                $fileName = $memorial->id."_profile.".$lowerExtentsion;
                $profilePathFileName = $this->S3_PATH_PROFILE.$fileName;

                $exists = Storage::disk('s3')->exists($profilePathFileName);
                if ($exists) {
                    Storage::disk('s3')->delete($profilePathFileName);
                }
                Storage::disk('s3')->put($profilePathFileName, file_get_contents($profile_url));

                Attachment::where('id', $memorial->profile_attachment_id)->delete();

                $profileAttachment = new Attachment();
                $profileAttachment->file_path = $this->S3_PATH_PROFILE;
                $profileAttachment->file_name = $fileName;
                $profileAttachment->save();

                $updateColumn['profile_attachment_id'] = $profileAttachment->id;
            }

            // BGM 업로드
            $bgm_url = $request->file('bgm');
            if ($bgm_url) {
                $file = $request->file('bgm')->getClientOriginalName();
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $lowerExtentsion = strtolower($extension);
                $fileName = $memorial->id."_bgm.".$lowerExtentsion;
                $bgmPathFileName = $this->S3_PATH_BGM.$fileName;

                $exists = Storage::disk('s3')->exists($bgmPathFileName);
                if ($exists) {
                    Storage::disk('s3')->delete($bgmPathFileName);
                }
                Storage::disk('s3')->put($bgmPathFileName, file_get_contents($bgm_url));

                Attachment::where('id', $memorial->bgm_attachment_id)->delete();

                $bgmAttachment = new Attachment();
                $bgmAttachment->file_path = $this->S3_PATH_BGM;
                $bgmAttachment->file_name = $fileName;
                $bgmAttachment->save();

                $updateColumn['bgm_attachment_id'] = $bgmAttachment->id;
            }

            Memorial::where('id', $id)->update($updateColumn);

            DB::commit();

            return response()->json([
                'result' => 'success',
                'message' => '기념관 수정에 성공하였습니다.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => 'fail',
                'message' => '기념관 수정에 실패하였습니다. ['.$e->getMessage().']'
            ]);
        }
    }

    public function detail(Request $request, $id) {
        if (is_null($id)) {
            return response()->json([
                'result' => 'fail',
                'message' => '기념관 ID가 없습니다.'
            ]);
        }

        $memorial = Memorial::with(['attachmentProfileImage', 'attachmentBgm', 'story', 'visitComments'])
            ->join('mm_users as user', 'mm_memorials.user_id', 'user.id')
            ->select('mm_memorials.id', 'mm_memorials.user_id', 'mm_memorials.name', 'mm_memorials.birth_start', 'mm_memorials.birth_end', 'mm_memorials.career_contents', 'mm_memorials.is_open', 'mm_memorials.profile_attachment_id', 'mm_memorials.bgm_attachment_id', 'mm_memorials.created_at', 'mm_memorials.updated_at')
            ->where('mm_memorials.id', $id)->first();

        if (is_null($memorial)) {
            return response()->json([
                'result' => 'fail',
                'message' => '기념관 정보가 없습니다.'
            ]);
        }
        return response()->json([
            'result' => 'success',
            'message' => '기념관 조회가 성공하였습니다.',
            'data' => $memorial
        ]);
    }

    public function view(Request $request) {
        $userId = Auth::user()->id;

        $memorial = Memorial::with(['attachmentProfileImage', 'attachmentBgm'])
            ->where('user_id', $userId)->first();

        return response()->json([
            'result' => 'success',
            'message' => '기념관 조회가 성공하였습니다.',
            'data' => $memorial
        ]);
    }

    public function index(Request $request) {
        $memorial = Memorial::with(['attachmentProfileImage', 'attachmentBgm'])
            ->join('mm_users as user', 'mm_memorials.user_id', 'user.id')
            ->select('mm_memorials.id', 'mm_memorials.user_id', 'user.user_name', 'mm_memorials.career_contents', 'mm_memorials.profile_attachment_id', 'mm_memorials.bgm_attachment_id')
            ->where('mm_memorials.is_open', 1)
            ->orderBy('mm_memorials.created_at', 'desc')
            ->limit(12)->get();

        return response()->json([
            'result' => 'success',
            'message' => '최근 등록된 12개의 기념관 조회가 성공하였습니다.',
            'data' => $memorial
        ]);
    }
}
