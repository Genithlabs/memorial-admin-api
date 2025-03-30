<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Memorial;
use App\Models\Story;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Cloudinary\Cloudinary;

class StoryController extends Controller
{
    private $S3_PATH_STORY_ATTACHMENT;

    protected $cloudinary;

    public function __construct(Cloudinary $cloudinary) {
        $this->S3_PATH_STORY_ATTACHMENT = "/memorial/story/";
        $this->cloudinary = $cloudinary;
    }

    public function register(Request $request, $memorialId) {
        // 유효성 체크
        if (is_null($memorialId)) {
            return response()->json([
                'result' => 'fail',
                'message' => '기념관 ID가 없습니다.'
            ]);
        }

        $userId = Auth::user()->id;

        $memorial = Memorial::where('id', $memorialId)->where('user_id', $userId)->first();
        if (is_null($memorial)) {
            return response()->json([
                'result' => 'fail',
                'message' => '기념관 스토리를 등록할 권한이 없습니다.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required',
            'attachment' => 'sometimes|max:10240'
        ], [
            'title.required' => '제목을 입력해 주세요',
            'title.max' => '제목은 255자 이내로 입력해 주세요',
            'message.required' => '스토리를 입력해 주세요',
            'attachment.max' => '첨부파일은 10Mb 이하여야 합니다'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'result' => 'fail',
                'message' => $validator->errors()->all()
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = request()->only('title', 'message', 'attachment');

        try {
            DB::beginTransaction();

            $story = new Story();
            $story->user_id = $userId;
            $story->memorial_id = $memorialId;
            $story->title = $data['title'];
            $story->message = $data['message'];
            $story->save();

            // 첨부파일 업로드
            $attachment_url = $request->file('attachment');
            if ($attachment_url) {
                $file = $request->file('attachment')->getClientOriginalName();
                $mimeType = $attachment_url->getMimeType();
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $lowerExtentsion = strtolower($extension);
                $fileName = $story->id;

                if (str_starts_with($mimeType, 'image/')) {
                    $resourceType = "image";
                } elseif (str_starts_with($mimeType, 'video/')) {
                    $resourceType = "video";
                } else {
                    throw new Exception("지원하지 않은 파일타입입니다.: " . $mimeType);
                }

                $profileUploadResponse = $this->cloudinary->uploadApi()->upload($attachment_url->getRealPath(), $options = [
                    'public_id' => $fileName,
                    'asset_folder' => $this->S3_PATH_STORY_ATTACHMENT,
                    'resource_type' => $resourceType,
                    'use_filename' => true, // 원본 파일명을 public_id 로 사용함
                ]);

                $publicId = $profileUploadResponse['public_id'];
                $version = $profileUploadResponse['version'];

                $filePath = "/".env('CLOUDINARY_NAME')."/".$resourceType."/upload/v".$version."/";
                $fileName = $publicId.".".$lowerExtentsion;

                $attachment = new Attachment();
                $attachment->file_path = $filePath;
                $attachment->file_name = $fileName;
                $attachment->save();

                $story->attachment_id = $attachment->id;
                $story->save();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => 'fail',
                'message' => '스토리 생성에 실패하였습니다. ['.$e->getMessage().']'
            ]);
        }

        // 모든 스토리 목록을 리턴합니다.
        $storyList = Story::with('attachment')
            ->join('mm_users as user', 'mm_stories.user_id', 'user.id')
            ->select('mm_stories.id', 'mm_stories.user_id', 'user.user_name', 'mm_stories.memorial_id', 'mm_stories.title', 'mm_stories.message', 'mm_stories.attachment_id', 'mm_stories.is_visible', 'mm_stories.created_at', 'mm_stories.updated_at')
            ->where('mm_stories.memorial_id', $memorialId)
            ->where('mm_stories.user_id', $userId)
            ->where('mm_stories.is_visible', 1)->orderBy('mm_stories.created_at', 'desc')
            ->get();

        return response()->json([
            'result' => 'success',
            'message' => '스토리 생성에 성공하였습니다.',
            'data' => $storyList
        ]);
    }

    public function list(Request $request, $memorialId) {
        // 유효성 체크
        if (is_null($memorialId)) {
            return response()->json([
                'result' => 'fail',
                'message' => '기념관 ID가 없습니다.'
            ]);
        }

        // 모든 스토리 목록을 리턴합니다.
        $storyList = Story::with('attachment')
            ->join('mm_users as user', 'mm_stories.user_id', 'user.id')
            ->select('mm_stories.id', 'mm_stories.user_id', 'user.user_name', 'mm_stories.memorial_id', 'mm_stories.title', 'mm_stories.message', 'mm_stories.attachment_id', 'mm_stories.is_visible', 'mm_stories.created_at', 'mm_stories.updated_at')
            ->where('mm_stories.memorial_id', $memorialId)
            ->where('mm_stories.is_visible', 1)->orderBy('mm_stories.created_at', 'desc')
            ->get();

        return response()->json([
            'result' => 'success',
            'message' => '스토리 조회가 성공하였습니다.',
            'data' => $storyList
        ]);
    }

    public function delete(Request $request, $memorialId) {
        // 유효성 체크
        // 1. 기념관 ID 존재 유무 체크
        if (is_null($memorialId)) {
            return response()->json([
                'result' => 'fail',
                'message' => '기념관 ID가 없습니다.'
            ]);
        }

        // 2. 스토리 ID 존재 유무 체크
        $validator = Validator::make($request->all(), [
            'story_id' => 'required'
        ], [
            'story_id.required' => '삭제할 스토리 ID가 없습니다.'
        ]);

        $data = request()->only('story_id');

        // 3. 삭제하려는 스토리가 존재하는지 체크함
        $story = Story::join('mm_users as user', 'mm_stories.user_id', 'user.id')
            ->select('mm_stories.id', 'mm_stories.user_id', 'user.user_name', 'mm_stories.memorial_id', 'mm_stories.title', 'mm_stories.message', 'mm_stories.attachment_id', 'mm_stories.is_visible', 'mm_stories.created_at', 'mm_stories.updated_at')
            ->where('user.id', auth()->user()->id)
            ->where('mm_stories.memorial_id', $memorialId)
            ->where('mm_stories.id', $data['story_id'])
            ->first();

        if (is_null($story)) {
            return response()->json([
                'result' => 'fail',
                'message' => '스토리 삭제 권한이 없습니다.'
            ]);
        }

        $attachment = null;
        if (!is_null($story->attachment_id)) {
            $attachment = Attachment::where('id', $story->attachment_id);
        }

        try {
            DB::beginTransaction();

            if (!is_null($attachment)) {
                $fileInfo = pathinfo($story->attachment->file_name);
                $publicId = $fileInfo['filename'];
                $assetExistResponse = $this->cloudinary->adminApi()->assetsByIds($publicId);
                if (!empty($assetExistResponse['resources'])) {
                    $assetDeleteResponse = $this->cloudinary->adminApi()->deleteAssets($publicId);
                }
                $story->attachment->delete();
            }

            $story->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => 'fail',
                'message' => '스토리 삭제에 실패하였습니다. ['.$e->getMessage().']'
            ]);
        }

        return response()->json([
            'result' => 'success',
            'message' => '스토리가 삭제되었습니다.'
        ]);
    }
}
