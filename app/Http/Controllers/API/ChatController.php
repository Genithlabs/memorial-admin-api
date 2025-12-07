<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MemorialQuestion;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * 기념관 생성에 필요한 질문 리스트를 반환합니다.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQuestions(Request $request)
    {
        $questions = MemorialQuestion::with('detail')
            ->where('is_active', 1)
            ->orderBy('display_order', 'asc')
            ->get();

        $nameQuestion = $questions->first(function ($question) {
            return $question->detail && $question->detail->question_type === 'name';
        });
        $birthStartQuestion = $questions->first(function ($question) {
            return $question->detail && $question->detail->question_type === 'birth_start';
        });
        $profileQuestion = $questions->first(function ($question) {
            return $question->detail && $question->detail->question_type === 'profile';
        });
        $questionList = $questions->filter(function ($question) {
            return $question->detail && $question->detail->question_type === 'question';
        })
            ->map(function ($question) {
                return $question->detail ? $question->detail->question_title : null;
            })
            ->filter()
            ->values()
            ->toArray();

        $data = [
            'name' => $nameQuestion && $nameQuestion->detail ? $nameQuestion->detail->question_title : '안녕하세요! 본인의 이름을 알려주세요.',
            'birth_start' => $birthStartQuestion && $birthStartQuestion->detail ? $birthStartQuestion->detail->question_title : '태어난 날짜는 언제인가요?',
            'questions' => !empty($questionList) ? $questionList : [
                '어린 시절은 어디에서 보내셨나요?',
                '웃음을 주는 어린 시절의 기억은?',
                '인생에서 가장 큰 영향을 준 사람은?',
                '인생에서 가장 자랑스러운 순간은?',
                '극복했던 가장 힘든 도전은?',
                '젊은 세대에게 전하고 싶은 조언은?',
                '가족이나 친구에게 남기고 싶은 메시지?'
            ],
            'profile' => $profileQuestion && $profileQuestion->detail ? $profileQuestion->detail->question_title : '마지막으로 프로필 사진을 업로드해주세요.'
        ];

        return response()->json([
            'result' => 'success',
            'message' => '',
            'data' => $data
        ]);
    }
}
