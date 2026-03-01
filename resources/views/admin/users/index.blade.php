<x-admin-layout title="회원 관리">
    {{-- Search & Filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="아이디, 이름, 이메일 검색..."
                       class="w-full border-gray-300 rounded-lg text-sm focus:border-[#c8952e] focus:ring-[#c8952e]">
            </div>
            <select name="filter" class="border-gray-300 rounded-lg text-sm focus:border-[#c8952e] focus:ring-[#c8952e]">
                <option value="">전체</option>
                <option value="admin" {{ request('filter') === 'admin' ? 'selected' : '' }}>관리자</option>
                <option value="normal" {{ request('filter') === 'normal' ? 'selected' : '' }}>일반</option>
                <option value="trial" {{ request('filter') === 'trial' ? 'selected' : '' }}>체험판</option>
                <option value="dormancy" {{ request('filter') === 'dormancy' ? 'selected' : '' }}>휴면</option>
                <option value="withdraw" {{ request('filter') === 'withdraw' ? 'selected' : '' }}>탈퇴</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-[#c8952e] text-white rounded-lg text-sm font-medium hover:bg-[#b5852a] transition">검색</button>
        </form>
    </div>

    {{-- Desktop Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hidden md:block">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">ID</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">아이디</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">이름</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">이메일</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">타입</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">상태</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">가입일</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                            <td class="py-3 px-4 text-gray-400">{{ $user->id }}</td>
                            <td class="py-3 px-4 font-medium text-gray-900">{{ $user->user_id }}</td>
                            <td class="py-3 px-4">{{ $user->user_name }}</td>
                            <td class="py-3 px-4 text-gray-500">{{ $user->email }}</td>
                            <td class="py-3 px-4">
                                @if($user->is_admin)
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-[#c8952e]/10 text-[#c8952e]">관리자</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">일반</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex flex-wrap gap-1">
                                    @if($user->is_trial)
                                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">체험판</span>
                                    @endif
                                    @if($user->is_dormancy)
                                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">휴면</span>
                                    @endif
                                    @if($user->is_withdraw)
                                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">탈퇴</span>
                                    @endif
                                    @if(!$user->is_trial && !$user->is_dormancy && !$user->is_withdraw)
                                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">정상</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4 text-gray-400">{{ $user->created_at->format('Y-m-d') }}</td>
                            <td class="py-3 px-4">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="text-[#c8952e] hover:underline text-sm">상세</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-8 text-center text-gray-400">회원이 없습니다.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-3">
        @forelse($users as $user)
            <a href="{{ route('admin.users.show', $user->id) }}" class="block bg-white rounded-xl shadow-sm border border-gray-100 p-4 active:bg-gray-50 transition">
                <div class="flex items-start justify-between mb-2">
                    <div class="min-w-0 flex-1">
                        <span class="text-xs text-gray-400">#{{ $user->id }}</span>
                        <h4 class="text-sm font-semibold text-gray-900">{{ $user->user_name }}</h4>
                        <p class="text-xs text-gray-500">{{ $user->user_id }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-1 ml-3 shrink-0">
                        @if($user->is_admin)
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-[#c8952e]/10 text-[#c8952e] leading-normal">관리자</span>
                        @else
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 leading-normal">일반</span>
                        @endif
                        @if($user->is_trial)
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 leading-normal">체험판</span>
                        @elseif($user->is_dormancy)
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 leading-normal">휴면</span>
                        @elseif($user->is_withdraw)
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 leading-normal">탈퇴</span>
                        @else
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 leading-normal">정상</span>
                        @endif
                    </div>
                </div>
                <div class="text-xs text-gray-500">
                    <span>{{ $user->email }}</span>
                    <span class="mx-1">·</span>
                    <span>{{ $user->created_at->format('Y-m-d') }}</span>
                </div>
            </a>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center text-gray-400">회원이 없습니다.</div>
        @endforelse

        @if($users->hasPages())
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
