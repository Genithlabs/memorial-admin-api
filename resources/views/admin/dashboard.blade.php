<x-admin-layout title="대시보드">
    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm text-gray-500">총 회원</p>
                    <p class="text-xl sm:text-3xl font-bold text-[#2c2520] mt-1">{{ number_format($stats['total_users']) }}</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm text-gray-500">총 기념관</p>
                    <p class="text-xl sm:text-3xl font-bold text-[#2c2520] mt-1">{{ number_format($stats['total_memorials']) }}</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-amber-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-[#c8952e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm text-gray-500">총 스토리</p>
                    <p class="text-xl sm:text-3xl font-bold text-[#2c2520] mt-1">{{ number_format($stats['total_stories']) }}</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm text-gray-500">총 방명록</p>
                    <p class="text-xl sm:text-3xl font-bold text-[#2c2520] mt-1">{{ number_format($stats['total_comments']) }}</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm text-gray-500">총 구매 신청</p>
                    <p class="text-xl sm:text-3xl font-bold text-[#2c2520] mt-1">{{ number_format($stats['total_purchases']) }}</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm text-gray-500">대기중 구매</p>
                    <p class="text-xl sm:text-3xl font-bold text-[#c8952e] mt-1">{{ number_format($stats['pending_purchases']) }}</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts & Tables Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Daily Signups Chart --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <h3 class="text-sm font-semibold text-[#2c2520] mb-4">최근 7일 가입 추이</h3>
            <div class="flex items-end gap-1 sm:gap-2 h-40">
                @php $maxCount = max(1, ...array_column($dailySignups, 'count')); @endphp
                @foreach($dailySignups as $day)
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <span class="text-xs text-gray-500">{{ $day['count'] }}</span>
                        <div class="w-full bg-[#c8952e]/20 rounded-t" style="height: {{ ($day['count'] / $maxCount) * 100 }}%">
                            <div class="w-full h-full bg-[#c8952e] rounded-t min-h-[4px]"></div>
                        </div>
                        <span class="text-[10px] sm:text-xs text-gray-400">{{ $day['date'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Recent Users --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-[#2c2520]">최근 가입 회원</h3>
                <a href="{{ route('admin.users.index') }}" class="text-xs text-[#c8952e] hover:underline">전체 보기</a>
            </div>
            <div class="space-y-3">
                @forelse($recentUsers as $user)
                    <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $user->user_name }}</p>
                            <p class="text-xs text-gray-400">{{ $user->user_id }}</p>
                        </div>
                        <span class="text-xs text-gray-400">{{ $user->created_at->format('Y-m-d') }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">가입 회원이 없습니다.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Purchases --}}
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-[#2c2520]">최근 구매 신청</h3>
            <a href="{{ route('admin.purchases.index') }}" class="text-xs text-[#c8952e] hover:underline">전체 보기</a>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-2 px-3 text-gray-500 font-medium">회원</th>
                        <th class="text-left py-2 px-3 text-gray-500 font-medium">상태</th>
                        <th class="text-left py-2 px-3 text-gray-500 font-medium">신청일</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPurchases as $purchase)
                        <tr class="border-b border-gray-50 last:border-0">
                            <td class="py-2.5 px-3">{{ $purchase->user->user_name ?? '-' }}</td>
                            <td class="py-2.5 px-3">
                                @if($purchase->status === 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">대기</span>
                                @elseif($purchase->status === 'approved')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">승인</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">거절</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-gray-400">{{ $purchase->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-4 text-center text-gray-400">구매 신청이 없습니다.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile List --}}
        <div class="sm:hidden space-y-2">
            @forelse($recentPurchases as $purchase)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $purchase->user->user_name ?? '-' }}</p>
                        <p class="text-xs text-gray-400">{{ $purchase->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    @if($purchase->status === 'pending')
                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">대기</span>
                    @elseif($purchase->status === 'approved')
                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">승인</span>
                    @else
                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">거절</span>
                    @endif
                </div>
            @empty
                <p class="py-4 text-center text-sm text-gray-400">구매 신청이 없습니다.</p>
            @endforelse
        </div>
    </div>
</x-admin-layout>
