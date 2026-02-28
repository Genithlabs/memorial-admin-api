<x-admin-layout title="회원 상세">
    <div>
        {{-- Back --}}
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-[#c8952e] mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            회원 목록
        </a>

        {{-- Notice --}}
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.168 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
            </svg>
            <div class="text-sm text-amber-800">
                <p class="font-medium">개인정보 변경 시 주의사항</p>
                <p class="mt-1 text-amber-700">이름, 아이디, 이메일은 사용자가 직접 입력한 중요 개인정보입니다. 변경 시 사용자의 로그인 및 서비스 이용에 영향을 줄 수 있으므로 신중하게 처리해 주세요.</p>
            </div>
        </div>

        {{-- User Info (editable) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="text-base font-semibold text-[#2c2520] mb-4">기본 정보</h3>
            <form method="POST" action="{{ route('admin.users.update', $user->id) }}"
                  onsubmit="return confirm('회원의 기본 정보를 변경하시겠습니까? 로그인 및 서비스 이용에 영향을 줄 수 있습니다.')">
                @csrf
                @method('PATCH')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-500">ID</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $user->id }}</p>
                    </div>
                    <div>
                        <label for="user_id" class="text-xs text-gray-500">아이디</label>
                        <input type="text" id="user_id" name="user_id" value="{{ old('user_id', $user->user_id) }}"
                               class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c8952e]/50 focus:border-[#c8952e]">
                        @error('user_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="user_name" class="text-xs text-gray-500">이름</label>
                        <input type="text" id="user_name" name="user_name" value="{{ old('user_name', $user->user_name) }}"
                               class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c8952e]/50 focus:border-[#c8952e]">
                        @error('user_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="text-xs text-gray-500">이메일</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                               class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c8952e]/50 focus:border-[#c8952e]">
                        @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">연락처</label>
                        <p class="mt-1 text-sm font-medium text-gray-900 py-2">{{ $user->user_phone ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">가입일</label>
                        <p class="mt-1 text-sm font-medium text-gray-900 py-2">{{ $user->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">최근 로그인</label>
                        <p class="mt-1 text-sm font-medium text-gray-900 py-2">{{ $user->last_login_time ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">기념관</label>
                        <p class="mt-1 text-sm font-medium text-gray-900 py-2">
                            @if($user->memorial)
                                <a href="{{ route('admin.memorials.show', $user->memorial->id) }}" class="text-[#c8952e] hover:underline">
                                    {{ $user->memorial->name }}
                                </a>
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
                <div class="pt-4">
                    <button type="submit" class="px-4 py-2 bg-[#c8952e] text-white rounded-lg text-sm font-medium hover:bg-[#b5852a] transition">기본 정보 저장</button>
                </div>
            </form>
        </div>

        {{-- Status Management --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="text-base font-semibold text-[#2c2520] mb-4">상태 관리</h3>
            <form method="POST" action="{{ route('admin.users.update', $user->id) }}"
                  onsubmit="return confirm('회원 상태를 변경하시겠습니까?')">
                @csrf
                @method('PATCH')
                <div class="space-y-4">
                    <label class="flex items-center gap-3">
                        <input type="hidden" name="is_admin" value="0">
                        <input type="checkbox" name="is_admin" value="1" {{ $user->is_admin ? 'checked' : '' }}
                               class="rounded border-gray-300 text-[#c8952e] focus:ring-[#c8952e]">
                        <span class="text-sm text-gray-700">관리자 권한</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input type="hidden" name="is_dormancy" value="0">
                        <input type="checkbox" name="is_dormancy" value="1" {{ $user->is_dormancy ? 'checked' : '' }}
                               class="rounded border-gray-300 text-[#c8952e] focus:ring-[#c8952e]">
                        <span class="text-sm text-gray-700">휴면 처리</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input type="hidden" name="is_withdraw" value="0">
                        <input type="checkbox" name="is_withdraw" value="1" {{ $user->is_withdraw ? 'checked' : '' }}
                               class="rounded border-gray-300 text-[#c8952e] focus:ring-[#c8952e]">
                        <span class="text-sm text-gray-700">탈퇴 처리</span>
                    </label>
                    <div class="pt-2">
                        <button type="submit" class="px-4 py-2 bg-[#c8952e] text-white rounded-lg text-sm font-medium hover:bg-[#b5852a] transition">저장</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Delete --}}
        @if($user->user_id !== 'admin')
            <div class="bg-white rounded-xl shadow-sm border border-red-100 p-6">
                <h3 class="text-base font-semibold text-red-600 mb-2">회원 삭제</h3>
                <p class="text-sm text-gray-500 mb-4">회원을 삭제하면 관련 기념관, 스토리, 방명록이 모두 함께 삭제되며 복구할 수 없습니다.</p>
                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                      onsubmit="return confirm('정말 이 회원을 삭제하시겠습니까? 이 작업은 되돌릴 수 없습니다.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition">회원 삭제</button>
                </form>
            </div>
        @endif
    </div>
</x-admin-layout>
