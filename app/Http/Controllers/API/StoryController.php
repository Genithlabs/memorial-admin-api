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

class StoryController extends Controller
{
    private $S3_PATH_STORY_ATTACHMENT;

    public function __construct() {
        $this->S3_PATH_STORY_ATTACHMENT = "/memorial/story/";
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

        $valid = validator($request->only('title', 'message', 'attachment'), [
            'title' => 'required|string|max:255',
            'message' => 'required'
        ]);
        if ($valid->fails()) {
            return response()->json([
                'result' => 'fail',
                'message' => $valid->errors()->all()
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
            $file = $request->file('attachment')->getClientOriginalName();
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $lowerExtentsion = strtolower($extension);
            $fileName = $story->id.".".$lowerExtentsion;
            $attachmentPathFileName = $this->S3_PATH_STORY_ATTACHMENT.$fileName;

            $exists = Storage::disk('s3')->exists($attachmentPathFileName);
            if ($exists) {
                Storage::disk('s3')->delete($attachmentPathFileName);
            }
            Storage::disk('s3')->put($attachmentPathFileName, file_get_contents($attachment_url));

            $attachment = new Attachment();
            $attachment->file_path = $this->S3_PATH_STORY_ATTACHMENT;
            $attachment->file_name = $fileName;
            $attachment->save();

            $story->attachment_id = $attachment->id;
            $story->save();

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
}
