<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Memorial;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;

class MemorialController extends Controller
{
    private $S3_PATH_PROFILE;
    private $S3_PATH_BGM;
    private $S3_PATH_CAREER_CONTENT_FILE;
    private $S3_URL;

    public function __construct() {
        $this->S3_PATH_PROFILE = "/memorial/profile/";
        $this->S3_PATH_BGM = "/memorial/bgm/";
        $this->S3_PATH_CAREER_CONTENT_FILE = "/memorial/career/";
        $this->S3_URL = "https://foot-print-resources.s3.ap-northeast-2.amazonaws.com";
    }

    public function register(Request $request) {
        // 유효성 체크
        $memorial = Memorial::where('user_id', Auth::user()->user_id)->first();
        if (!is_null($memorial)) {
            return response()->json([
                'result' => 'fail',
                'message' => '이미 생성된 기념관이 존재합니다.'
            ]);
        }

        $valid = validator($request->only('user_name', 'birth_start', 'birth_end', 'profile'), [
            'user_name' => 'required|string|max:50',
            'birth_start' => 'required|sometimes|date_format:Y-m-d',
            'birth_end' => 'sometimes|date_format:Y-m-d',
            'profile' => 'required'
        ]);
        if ($valid->fails()) {
            return response()->json([
                'result' => 'fail',
                'message' => $valid->errors()->all()
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = request()->only('user_name', 'birth_start', 'birth_end', 'career', 'profile', 'bgm');

        try {
            DB::beginTransaction();

            $id = Auth::user()->id;
            $userId = Auth::user()->user_id;

            $memorial = new Memorial();
            $memorial->user_id = $userId;
            $memorial->name = $data['user_name'];
            $memorial->birth_start = $data['birth_start'];
            $memorial->birth_end = $data['birth_end'];
            $memorial->career_contents = $data['career'];
            $memorial->save();

            // 프로필 이미지 업로드
            $profile_url = $request->file('profile');
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

            $profileAttachment = new Attachment();
            $profileAttachment->file_path = $this->S3_PATH_PROFILE;
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
                $fileName = $memorial->id."_bgm.".$lowerExtentsion;
                $bgmPathFileName = $this->S3_PATH_BGM.$fileName;

                $exists = Storage::disk('s3')->exists($bgmPathFileName);
                if ($exists) {
                    Storage::disk('s3')->delete($bgmPathFileName);
                }
                Storage::disk('s3')->put($bgmPathFileName, file_get_contents($bgm_url));

                $bgmAttachment = new Attachment();
                $bgmAttachment->file_path = $this->S3_PATH_BGM;
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
        $valid = validator($request->only('career_contents_file'), [
            'career_contents_file' => 'required'
        ]);
        if ($valid->fails()) {
            return response()->json([
                'result' => 'fail',
                'message' => $valid->errors()->all()
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
            $fileName = Auth::user()->user_id."_".$randomString.".".$lowerExtentsion;
            $uploadPathFileName = $this->S3_PATH_CAREER_CONTENT_FILE.$fileName;
            Storage::disk('s3')->put($uploadPathFileName, file_get_contents($upload_file_url));

            return response()->json([
                'result' => 'success',
                'message' => '업로드가 성공하였습니다.',
                'url' => $this->S3_URL.$uploadPathFileName
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

        $valid = validator($request->only('user_name', 'birth_start', 'birth_end', 'profile'), [
            'user_name' => 'required|string|max:50',
            'birth_start' => 'required|sometimes|date_format:Y-m-d',
            'birth_end' => 'sometimes|date_format:Y-m-d',
            'profile' => 'required'
        ]);
        if ($valid->fails()) {
            return response()->json([
                'result' => 'fail',
                'message' => $valid->errors()->all()
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = request()->only('user_name', 'birth_start', 'birth_end', 'career', 'profile', 'bgm');

        try {
            DB::beginTransaction();

            $memorial = Memorial::where('id', $id)->first();

            $updateColumn = [
                'name' =>  $data['user_name'],
                'birth_start' => $data['birth_start'],
                'birth_end' => $data['birth_end'],
                'career_contents' => $data['career']
            ];

            // 프로필 이미지 업로드
            $profile_url = $request->file('profile');
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

        $memorial = Memorial::with(['attachmentProfileImage', 'attachmentBgm'])
            ->select('id', 'birth_start', 'birth_end', 'career_contents', 'is_open', 'profile_attachment_id', 'bgm_attachment_id', 'created_at', 'updated_at')
            ->where('id', $id)->first();

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
}
