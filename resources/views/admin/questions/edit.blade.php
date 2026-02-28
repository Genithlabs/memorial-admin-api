<x-admin-layout title="{{ $question ? '질문 수정' : '질문 추가' }}">
    <div>
        <a href="{{ route('admin.questions.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-[#c8952e] mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            질문 목록
        </a>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST"
                  action="{{ $question ? route('admin.questions.update', $question->id) : route('admin.questions.store') }}">
                @csrf
                @if($question) @method('PUT') @endif

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">질문 타입</label>
                        <select name="question_type"
                                class="w-full border-gray-300 rounded-lg text-sm focus:border-[#c8952e] focus:ring-[#c8952e]">
                            @foreach(['name' => '이름', 'birth_start' => '생년', 'question' => '질문', 'profile' => '프로필'] as $value => $label)
                                <option value="{{ $value }}" {{ old('question_type', $detail->question_type ?? '') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('question_type')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">질문 내용</label>
                        <textarea name="question_title" rows="3"
                                  class="w-full border-gray-300 rounded-lg text-sm focus:border-[#c8952e] focus:ring-[#c8952e]"
                                  placeholder="질문을 입력하세요...">{{ old('question_title', $detail->question_title ?? '') }}</textarea>
                        @error('question_title')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">표시 순서</label>
                        @if($question && in_array($question->display_order, [1, 2]))
                            <input type="number" value="{{ $question->display_order }}" disabled
                                   class="w-full border-gray-300 rounded-lg text-sm bg-gray-100 text-gray-500">
                            <p class="text-xs text-gray-400 mt-1">필수 질문은 순서를 변경할 수 없습니다.</p>
                        @else
                            <input type="number" name="display_order" min="3"
                                   value="{{ old('display_order', $question->display_order ?? 0) }}"
                                   class="w-full border-gray-300 rounded-lg text-sm focus:border-[#c8952e] focus:ring-[#c8952e]">
                            @error('display_order')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">활성화</label>
                        <select name="is_active"
                                class="w-full border-gray-300 rounded-lg text-sm focus:border-[#c8952e] focus:ring-[#c8952e]">
                            <option value="1" {{ old('is_active', $question->is_active ?? 1) == 1 ? 'selected' : '' }}>활성</option>
                            <option value="0" {{ old('is_active', $question->is_active ?? 1) == 0 ? 'selected' : '' }}>비활성</option>
                        </select>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="px-4 py-2 bg-[#c8952e] text-white rounded-lg text-sm font-medium hover:bg-[#b5852a] transition">
                            {{ $question ? '수정' : '추가' }}
                        </button>
                        <a href="{{ route('admin.questions.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">취소</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
