<x-admin-layout title="질문 관리">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">AI 자서전 생성에 사용되는 질문을 관리합니다.</p>
        <a href="{{ route('admin.questions.create') }}"
           class="px-4 py-2 bg-[#c8952e] text-white rounded-lg text-sm font-medium hover:bg-[#b5852a] transition shrink-0 ml-3">
            질문 추가
        </a>
    </div>
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-sm font-medium text-amber-800 mb-1">필수 질문 안내</p>
            <p class="text-sm text-amber-700">1, 2번 질문은 필수이며 순서가 고정됩니다. 순서 변경 및 삭제는 3번 이후 질문만 가능합니다.</p>
        </div>
    </div>

    {{-- Desktop Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hidden md:block">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">순서</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">타입</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">질문</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">활성화</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">관리</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($questions as $question)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                            <td class="py-3 px-4 text-gray-400">{{ $question->display_order }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $question->detail->question_type ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-medium text-gray-900">{{ $question->detail->question_title ?? '-' }}</td>
                            <td class="py-3 px-4">
                                @if($question->is_active)
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">활성</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">비활성</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.questions.edit', $question->id) }}" class="text-[#c8952e] hover:underline text-sm">수정</a>
                                    @if(!in_array($question->display_order, [1, 2]))
                                        <form method="POST" action="{{ route('admin.questions.destroy', $question->id) }}"
                                              onsubmit="return confirm('정말 삭제하시겠습니까?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:underline text-sm">삭제</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400">필수</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-gray-400">등록된 질문이 없습니다.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-3">
        @forelse($questions as $question)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400">{{ $question->display_order }}번</span>
                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $question->detail->question_type ?? '-' }}
                        </span>
                    </div>
                    @if($question->is_active)
                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 shrink-0 ml-2">활성</span>
                    @else
                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 shrink-0 ml-2">비활성</span>
                    @endif
                </div>
                <p class="text-sm font-medium text-gray-900 mb-3">{{ $question->detail->question_title ?? '-' }}</p>
                <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                    <a href="{{ route('admin.questions.edit', $question->id) }}" class="text-[#c8952e] hover:underline text-sm font-medium">수정</a>
                    @if(!in_array($question->display_order, [1, 2]))
                        <form method="POST" action="{{ route('admin.questions.destroy', $question->id) }}"
                              onsubmit="return confirm('정말 삭제하시겠습니까?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline text-sm">삭제</button>
                        </form>
                    @else
                        <span class="text-xs text-gray-400">필수</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center text-gray-400">등록된 질문이 없습니다.</div>
        @endforelse
    </div>
</x-admin-layout>
