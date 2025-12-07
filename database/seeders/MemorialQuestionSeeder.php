<?php

namespace Database\Seeders;

use App\Models\MemorialQuestion;
use App\Models\MemorialQuestionDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MemorialQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 이름 질문
        $nameDetail = MemorialQuestionDetail::create([
            'question_type' => 'name',
            'question_title' => '안녕하세요! 본인의 이름을 알려주세요.',
        ]);
        MemorialQuestion::create([
            'detail_id' => $nameDetail->id,
            'display_order' => 1,
            'is_active' => 1,
        ]);

        // 생년월일 질문
        $birthStartDetail = MemorialQuestionDetail::create([
            'question_type' => 'birth_start',
            'question_title' => '태어난 날짜는 언제인가요?',
        ]);
        MemorialQuestion::create([
            'detail_id' => $birthStartDetail->id,
            'display_order' => 2,
            'is_active' => 1,
        ]);

        // 일반 질문들
        $questions = [
            '어린 시절은 어디에서 보내셨나요?',
            '웃음을 주는 어린 시절의 기억은?',
            '인생에서 가장 큰 영향을 준 사람은?',
            '인생에서 가장 자랑스러운 순간은?',
            '극복했던 가장 힘든 도전은?',
            '젊은 세대에게 전하고 싶은 조언은?',
            '가족이나 친구에게 남기고 싶은 메시지?',
        ];

        $order = 3;
        foreach ($questions as $questionTitle) {
            $detail = MemorialQuestionDetail::create([
                'question_type' => 'question',
                'question_title' => $questionTitle,
            ]);
            MemorialQuestion::create([
                'detail_id' => $detail->id,
                'display_order' => $order,
                'is_active' => 1,
            ]);
            $order++;
        }

        // 프로필 사진 질문
        $profileDetail = MemorialQuestionDetail::create([
            'question_type' => 'profile',
            'question_title' => '마지막으로 프로필 사진을 업로드해주세요.',
        ]);
        MemorialQuestion::create([
            'detail_id' => $profileDetail->id,
            'display_order' => $order,
            'is_active' => 1,
        ]);
    }
}
