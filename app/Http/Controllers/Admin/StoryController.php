<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Memorial;
use App\Models\Story;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    public function index($id)
    {
        $memorial = Memorial::findOrFail($id);

        $stories = Story::with('user')
            ->where('memorial_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.memorials.stories', compact('memorial', 'stories'));
    }

    public function toggle($memorialId, $storyId)
    {
        $story = Story::where('memorial_id', $memorialId)->findOrFail($storyId);
        $story->is_visible = $story->is_visible ? 0 : 1;
        $story->save();

        $status = $story->is_visible ? '노출' : '숨김';

        AdminLog::log('스토리 노출 변경', '스토리', $story->id, "스토리 #{$story->id} {$status} 처리");

        return redirect()->route('admin.memorials.stories.index', $memorialId)->with('success', "스토리가 {$status} 처리되었습니다.");
    }

    public function update(Request $request, $memorialId, $storyId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'nullable|string|max:10000',
        ]);

        $story = Story::where('memorial_id', $memorialId)->findOrFail($storyId);

        $changes = [];
        if ($story->title !== $request->input('title')) {
            $changes[] = "제목: {$story->title} → {$request->input('title')}";
        }
        if ($story->message !== $request->input('message')) {
            $changes[] = "내용 변경";
        }

        $story->title = $request->input('title');
        $story->message = $request->input('message');
        $story->save();

        if ($changes) {
            AdminLog::log('스토리 수정', '스토리', $story->id, "스토리 #{$story->id} " . implode(', ', $changes));
        }

        return redirect()->route('admin.memorials.stories.index', $memorialId)->with('success', '스토리가 수정되었습니다.');
    }

    public function destroy($memorialId, $storyId)
    {
        Story::where('memorial_id', $memorialId)->findOrFail($storyId)->delete();

        AdminLog::log('스토리 삭제', '스토리', $storyId, "스토리 #{$storyId} 삭제");

        return redirect()->route('admin.memorials.stories.index', $memorialId)->with('success', '스토리가 삭제되었습니다.');
    }
}
