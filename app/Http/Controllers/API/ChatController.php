<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MemorialQuestion;
use App\Services\MemorialService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    protected $memorialService;

    public function __construct(MemorialService $memorialService)
    {
        $this->memorialService = $memorialService;
    }

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

    /**
     * Chat 플로우를 통해 기념관을 생성합니다.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submit(Request $request)
    {
        // 유효성 체크
        $memorial = $this->memorialService->checkExistingMemorial(Auth::user()->id);
        if (!is_null($memorial)) {
            return response()->json([
                'result' => 'fail',
                'message' => '이미 생성된 기념관이 존재합니다.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'birth_start' => 'required|string',
            'prompts' => 'required|string',
            'profile' => 'required|mimes:jpeg,jpg,png|max:10240',
        ], [
            'name.required' => '이름을 입력해 주세요',
            'name.max' => '이름은 50자 이내로 입력해 주세요',
            'birth_start.required' => '태어난 날짜를 입력해 주세요',
            'prompts.required' => '질문 답변을 입력해 주세요',
            'profile.required' => '프로필 이미지를 업로드해 주세요',
            'profile.mimes' => '프로필 이미지는 jpg/jpeg/png 형식이여야 합니다',
            'profile.max' => '프로필 이미지는 10Mb 이하여야 합니다',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'result' => 'fail',
                'message' => $validator->errors()->all()
            ], Response::HTTP_BAD_REQUEST);
        }

        // 데이터 변환
        // name → user_name
        // birth_start → 날짜 형식 변환 (필요시)
        // prompts → career
        // profile → profile (파일)
        $birthStart = $request->input('birth_start');
        // 날짜 형식이 Y-m-d가 아닐 수 있으므로 변환 시도
        try {
            $birthStartDate = Carbon::parse($birthStart)->format('Y-m-d');
        } catch (\Exception $e) {
            return response()->json([
                'result' => 'fail',
                'message' => '생년월일 형식이 올바르지 않습니다.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = [
            'user_name' => $request->input('name'),
            'birth_start' => $birthStartDate,
            'career' => $request->input('prompts'),
            'profile' => $request->file('profile'),
            'bgm' => null, // Chat 플로우에서는 BGM 없음
        ];

        // MemorialService를 통해 기념관 생성
        $result = $this->memorialService->createMemorial($data, Auth::user()->id);

        if ($result['success']) {
            return response()->json([
                'result' => 'success',
                'message' => $result['message'],
                'data' => [
                    'id' => $result['memorial']->id
                ]
            ]);
        } else {
            return response()->json([
                'result' => 'fail',
                'message' => $result['message']
            ]);
        }
    }
}
