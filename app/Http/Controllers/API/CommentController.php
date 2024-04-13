<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\VisitorComment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function __construct() {

    }

    public function register(Request $request, $memorialId) {
        // 유효성 체크
        if (is_null($memorialId)) {
            return response()->json([
                'result' => 'fail',
                'message' => '기념관 ID가 없습니다.'
            ]);
        }

        $valid = validator($request->only('message'), [
            'message' => 'required'
        ]);
        if ($valid->fails()) {
            return response()->json([
                'result' => 'fail',
                'message' => $valid->errors()->all()
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = request()->only('message');

        try {
            DB::beginTransaction();

            $userId = Auth::user()->id;

            $comment = new VisitorComment();
            $comment->user_id = $userId;
            $comment->memorial_id = $memorialId;
            $comment->message = $data['message'];
            $comment->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => 'fail',
                'message' => '코멘트 생성에 실패하였습니다. ['.$e->getMessage().']'
            ]);
        }

        // 모든 코멘트 목록을 리턴합니다.
        $commentList = VisitorComment::join('mm_users as user', 'mm_visitor_comments.user_id', 'user.id')
            ->select('mm_visitor_comments.id', 'mm_visitor_comments.user_id', 'user.user_name', 'mm_visitor_comments.memorial_id', 'mm_visitor_comments.message', 'mm_visitor_comments.is_visible', 'mm_visitor_comments.created_at', 'mm_visitor_comments.updated_at')
            ->where('mm_visitor_comments.memorial_id', $memorialId)
            ->where('mm_visitor_comments.is_visible', 1)->orderBy('mm_visitor_comments.created_at', 'desc')
            ->get();

        return response()->json([
            'result' => 'success',
            'message' => '코멘트 생성에 성공하였습니다.',
            'data' => $commentList
        ]);
    }
}
