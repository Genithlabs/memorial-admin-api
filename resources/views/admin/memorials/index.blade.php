<x-admin-layout title="기념관 관리">
    {{-- Search --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('admin.memorials.index') }}" class="flex gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="기념관명, 회원 검색..."
                       class="w-full border-gray-300 rounded-lg text-sm focus:border-[#c8952e] focus:ring-[#c8952e]">
            </div>
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
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">기념관명</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">소유자</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">생년</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">공개여부</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">생성일</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($memorials as $memorial)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                            <td class="py-3 px-4 text-gray-400">{{ $memorial->id }}</td>
                            <td class="py-3 px-4 font-medium text-gray-900">{{ $memorial->name }}</td>
                            <td class="py-3 px-4 text-gray-500">{{ $memorial->user->user_name ?? '-' }} ({{ $memorial->user->user_id ?? '-' }})</td>
                            <td class="py-3 px-4 text-gray-500">{{ $memorial->birth_start ?? '-' }}</td>
                            <td class="py-3 px-4">
                                @if($memorial->is_open)
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">공개</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">비공개</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-400">{{ $memorial->created_at->format('Y-m-d') }}</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.memorials.show', $memorial->id) }}" class="text-[#c8952e] hover:underline text-sm">상세</a>
                                    <a href="{{ route('admin.memorials.stories.index', $memorial->id) }}" class="text-gray-500 hover:text-[#c8952e] hover:underline text-sm">스토리</a>
                                    <a href="{{ route('admin.memorials.comments.index', $memorial->id) }}" class="text-gray-500 hover:text-[#c8952e] hover:underline text-sm">방명록</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-gray-400">기념관이 없습니다.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($memorials->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $memorials->links() }}
            </div>
        @endif
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-3">
        @forelse($memorials as $memorial)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <span class="text-xs text-gray-400">#{{ $memorial->id }}</span>
                        <h4 class="text-sm font-semibold text-gray-900">{{ $memorial->name }}</h4>
                    </div>
                    @if($memorial->is_open)
                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">공개</span>
                    @else
                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">비공개</span>
                    @endif
                </div>
                <div class="text-xs text-gray-500 space-y-1 mb-3">
                    <p>소유자: {{ $memorial->user->user_name ?? '-' }} ({{ $memorial->user->user_id ?? '-' }})</p>
                    <p>생년: {{ $memorial->birth_start ?? '-' }} · 생성일: {{ $memorial->created_at->format('Y-m-d') }}</p>
                </div>
                <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                    <a href="{{ route('admin.memorials.show', $memorial->id) }}" class="text-[#c8952e] hover:underline text-sm font-medium">상세</a>
                    <a href="{{ route('admin.memorials.stories.index', $memorial->id) }}" class="text-gray-500 hover:text-[#c8952e] hover:underline text-sm">스토리</a>
                    <a href="{{ route('admin.memorials.comments.index', $memorial->id) }}" class="text-gray-500 hover:text-[#c8952e] hover:underline text-sm">방명록</a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center text-gray-400">기념관이 없습니다.</div>
        @endforelse

        @if($memorials->hasPages())
            <div class="mt-4">
                {{ $memorials->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
