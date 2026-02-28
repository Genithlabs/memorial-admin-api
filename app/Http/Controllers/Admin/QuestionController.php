<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\MemorialQuestion;
use App\Models\MemorialQuestionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = MemorialQuestion::with('detail')
            ->orderBy('display_order', 'asc')
            ->get();

        return view('admin.questions.index', compact('questions'));
    }

    public function create()
    {
        return view('admin.questions.edit', [
            'question' => null,
            'detail' => null,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'question_type' => 'required|string|max:50',
            'question_title' => 'required|string|max:255',
            'display_order' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $question = DB::transaction(function () use ($request) {
            $detail = MemorialQuestionDetail::create([
                'question_type' => $request->input('question_type'),
                'question_title' => $request->input('question_title'),
            ]);

            return MemorialQuestion::create([
                'detail_id' => $detail->id,
                'display_order' => $request->input('display_order'),
                'is_active' => $request->input('is_active'),
            ]);
        });

        AdminLog::log('질문 추가', '질문', $question->id, "{$request->input('question_title')} 질문 추가");

        return redirect()->route('admin.questions.index')->with('success', '질문이 추가되었습니다.');
    }

    public function edit($id)
    {
        $question = MemorialQuestion::with('detail')->findOrFail($id);
        $detail = $question->detail;

        return view('admin.questions.edit', compact('question', 'detail'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question_type' => 'required|string|max:50',
            'question_title' => 'required|string|max:255',
            'display_order' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $question = MemorialQuestion::with('detail')->findOrFail($id);

        // 필수 질문(1, 2번)은 순서 변경 불가
        $isRequired = in_array($question->display_order, [1, 2]);

        DB::transaction(function () use ($request, $question, $isRequired) {
            $question->detail->update([
                'question_type' => $request->input('question_type'),
                'question_title' => $request->input('question_title'),
            ]);

            $updateData = ['is_active' => $request->input('is_active')];
            if (!$isRequired) {
                $updateData['display_order'] = $request->input('display_order');
            }
            $question->update($updateData);
        });

        AdminLog::log('질문 수정', '질문', $question->id, "{$request->input('question_title')} 질문 수정");

        return redirect()->route('admin.questions.index')->with('success', '질문이 수정되었습니다.');
    }

    public function destroy($id)
    {
        $question = MemorialQuestion::with('detail')->findOrFail($id);

        if (in_array($question->display_order, [1, 2])) {
            return redirect()->back()->with('error', '필수 질문은 삭제할 수 없습니다.');
        }

        $questionId = $question->id;
        $questionTitle = $question->detail?->question_title ?? '';

        DB::transaction(function () use ($question) {
            $question->delete();
            if ($question->detail) {
                $question->detail->delete();
            }
        });

        AdminLog::log('질문 삭제', '질문', $questionId, "{$questionTitle} 질문 삭제");

        return redirect()->route('admin.questions.index')->with('success', '질문이 삭제되었습니다.');
    }
}
