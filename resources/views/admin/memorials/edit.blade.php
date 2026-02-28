<x-admin-layout title="기념관 정보 수정">
    <div>
        {{-- Back --}}
        <a href="{{ route('admin.memorials.show', $memorial->id) }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-[#c8952e] mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            기념관 상세
        </a>

        {{-- Notice --}}
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.168 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
            </svg>
            <div class="text-sm text-amber-800">
                <p class="font-medium">기념관 정보 변경 시 주의사항</p>
                <p class="mt-1 text-amber-700">기념관은 사용자가 소중한 사람을 기리기 위해 직접 작성한 공간입니다. 변경 내용은 앱과 웹에 즉시 반영되므로 신중하게 처리해 주세요.</p>
            </div>
        </div>

        {{-- Edit Form --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-semibold text-[#2c2520] mb-4">기본 정보 수정</h3>
            <form method="POST" action="{{ route('admin.memorials.update', $memorial->id) }}"
                  onsubmit="return confirm('기념관 기본 정보를 변경하시겠습니까? 변경 내용이 앱과 웹에 즉시 반영됩니다.')">
                @csrf
                @method('PATCH')
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-xs font-medium text-gray-500 mb-1">기념관명</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $memorial->name) }}" maxlength="50"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c8952e]/50 focus:border-[#c8952e]">
                        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="birth_start" class="block text-xs font-medium text-gray-500 mb-1">출생</label>
                            <input type="date" id="birth_start" name="birth_start" value="{{ old('birth_start', $memorial->birth_start) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c8952e]/50 focus:border-[#c8952e]">
                            @error('birth_start') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="birth_end" class="block text-xs font-medium text-gray-500 mb-1">서거</label>
                            <input type="date" id="birth_end" name="birth_end" value="{{ old('birth_end', $memorial->birth_end) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c8952e]/50 focus:border-[#c8952e]">
                            @error('birth_end') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label for="career_contents" class="block text-xs font-medium text-gray-500 mb-1">생애</label>
                        <textarea id="career_contents" name="career_contents" rows="20"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c8952e]/50 focus:border-[#c8952e] font-mono">{{ old('career_contents', $memorial->career_contents) }}</textarea>
                        <p class="mt-1 text-xs text-gray-400">HTML, 마크다운, 일반 텍스트 모두 지원됩니다.</p>
                        @error('career_contents') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex gap-3 pt-6">
                    <button type="submit" class="flex-1 sm:flex-none px-8 py-3 bg-[#c8952e] text-white rounded-lg text-base font-semibold hover:bg-[#b5852a] transition">저장</button>
                    <a href="{{ route('admin.memorials.show', $memorial->id) }}" class="flex-1 sm:flex-none px-8 py-3 text-base text-center text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg font-medium transition">취소</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
