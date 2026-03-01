<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Memorial;
use App\Models\Story;
use App\Models\VisitorComment;
use Illuminate\Http\Request;

class MemorialController extends Controller
{
    public function index(Request $request)
    {
        $query = Memorial::with('user');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('user_name', 'like', "%{$search}%")
                        ->orWhere('user_id', 'like', "%{$search}%");
                  });
            });
        }

        $memorials = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.memorials.index', compact('memorials'));
    }

    public function show($id)
    {
        $memorial = Memorial::with(['attachmentProfileImage', 'attachmentBgm', 'user'])
            ->withCount(['stories', 'comments'])
            ->findOrFail($id);

        $owner = $memorial->user;
        $storyCount = $memorial->stories_count;
        $commentCount = $memorial->comments_count;

        return view('admin.memorials.show', compact('memorial', 'owner', 'storyCount', 'commentCount'));
    }

    public function edit($id)
    {
        $memorial = Memorial::with('attachmentProfileImage', 'attachmentBgm')->findOrFail($id);

        return view('admin.memorials.edit', compact('memorial'));
    }

    public function update(Request $request, $id)
    {
        $memorial = Memorial::findOrFail($id);

        // 기본 정보 수정
        if ($request->has('name')) {
            $request->validate([
                'name' => 'required|string|max:50',
                'birth_start' => 'nullable|date',
                'birth_end' => 'nullable|date',
                'career_contents' => 'nullable|string|max:5000',
            ]);

            $changes = [];
            if ($memorial->name !== $request->input('name')) {
                $changes[] = "기념관명: {$memorial->name} → {$request->input('name')}";
            }
            if ((string)$memorial->birth_start !== $request->input('birth_start')) {
                $changes[] = "출생: " . ($memorial->birth_start ?? '미설정') . " → " . ($request->input('birth_start') ?: '미설정');
            }
            if ((string)$memorial->birth_end !== $request->input('birth_end')) {
                $changes[] = "서거: " . ($memorial->birth_end ?? '미설정') . " → " . ($request->input('birth_end') ?: '미설정');
            }
            if ($memorial->career_contents !== $request->input('career_contents')) {
                $changes[] = "생애 변경";
            }

            $memorial->name = $request->input('name');
            $memorial->birth_start = $request->input('birth_start') ?: null;
            $memorial->birth_end = $request->input('birth_end') ?: null;
            $memorial->career_contents = $request->input('career_contents');
            $memorial->save();

            if ($changes) {
                AdminLog::log('기념관 정보 수정', '기념관', $memorial->id, implode(', ', $changes));
            }

            return redirect()->route('admin.memorials.edit', $id)->with('success', '기념관 정보가 수정되었습니다.');
        }

        // 공개 설정 변경
        if ($request->has('is_open')) {
            $memorial->is_open = $request->boolean('is_open') ? 1 : 0;
            $memorial->save();

            AdminLog::log('기념관 설정 수정', '기념관', $memorial->id, "{$memorial->name} 공개설정 변경");
        }

        return redirect()->back()->with('success', '기념관 설정이 수정되었습니다.');
    }

    public function destroy($id)
    {
        $memorial = Memorial::findOrFail($id);

        // 연관 데이터 삭제
        Story::where('memorial_id', $id)->delete();
        VisitorComment::where('memorial_id', $id)->delete();

        AdminLog::log('기념관 삭제', '기념관', $memorial->id, "{$memorial->name} 기념관 삭제");

        $memorial->delete();

        return redirect()->route('admin.memorials.index')->with('success', '기념관이 삭제되었습니다.');
    }
}
