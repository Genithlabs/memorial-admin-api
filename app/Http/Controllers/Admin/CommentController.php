<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Memorial;
use App\Models\VisitorComment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index($id)
    {
        $memorial = Memorial::findOrFail($id);

        $comments = VisitorComment::with('user')
            ->where('memorial_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.memorials.comments', compact('memorial', 'comments'));
    }

    public function toggle($memorialId, $commentId)
    {
        $comment = VisitorComment::where('memorial_id', $memorialId)->findOrFail($commentId);
        $comment->is_visible = $comment->is_visible ? 0 : 1;
        $comment->save();

        $status = $comment->is_visible ? '노출' : '숨김';

        AdminLog::log('방명록 노출 변경', '방명록', $comment->id, "방명록 #{$comment->id} {$status} 처리");

        return redirect()->route('admin.memorials.comments.index', $memorialId)->with('success', "방명록이 {$status} 처리되었습니다.");
    }

    public function update(Request $request, $memorialId, $commentId)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $comment = VisitorComment::where('memorial_id', $memorialId)->findOrFail($commentId);
        $comment->message = $request->input('message');
        $comment->save();

        AdminLog::log('방명록 수정', '방명록', $comment->id, "방명록 #{$comment->id} 내용 변경");

        return redirect()->route('admin.memorials.comments.index', $memorialId)->with('success', '방명록이 수정되었습니다.');
    }

    public function destroy($memorialId, $commentId)
    {
        VisitorComment::where('memorial_id', $memorialId)->findOrFail($commentId)->delete();

        AdminLog::log('방명록 삭제', '방명록', $commentId, "방명록 #{$commentId} 삭제");

        return redirect()->route('admin.memorials.comments.index', $memorialId)->with('success', '방명록이 삭제되었습니다.');
    }
}
