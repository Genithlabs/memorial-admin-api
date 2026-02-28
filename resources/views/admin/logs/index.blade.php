<x-admin-layout title="활동 로그">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h1 class="text-2xl font-bold text-[#2c2520]">활동 로그</h1>
    </div>

    @php
        $labelStyles = [
            '전체'   => ['bg' => '#f3f4f6', 'color' => '#374151', 'ring' => '#6b7280'],
            '회원'   => ['bg' => '#dbeafe', 'color' => '#1e40af', 'ring' => '#3b82f6'],
            '기념관'  => ['bg' => '#f3e8ff', 'color' => '#6b21a8', 'ring' => '#a855f7'],
            '구매요청' => ['bg' => '#fee2e2', 'color' => '#991b1b', 'ring' => '#ef4444'],
            'AI 질문' => ['bg' => '#e0e7ff', 'color' => '#3730a3', 'ring' => '#6366f1'],
        ];
        $current = request('target_type', '');
        $filterKeys = ['' => '전체', '회원' => '회원', '기념관' => '기념관', '구매요청' => '구매요청', 'AI 질문' => 'AI 질문'];
    @endphp

    {{-- Filter --}}
    <div class="flex flex-wrap gap-2 mb-6">
        @foreach($filterKeys as $value => $label)
            @php $s = $labelStyles[$label]; @endphp
            <a href="{{ route('admin.logs.index', $value ? ['target_type' => $value] : []) }}"
               style="background-color: {{ $s['bg'] }}; color: {{ $s['color'] }};{{ $current === $value ? ' box-shadow: 0 0 0 2px #fff, 0 0 0 4px ' . $s['ring'] . ';' : '' }}"
               class="inline-block px-3 py-1 rounded-full text-xs font-semibold transition hover:opacity-80">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Desktop Table --}}
    <div class="hidden md:block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-6 py-3 text-left font-medium">일시</th>
                    <th class="px-6 py-3 text-left font-medium">관리자</th>
                    <th class="px-6 py-3 text-left font-medium">대상</th>
                    <th class="px-6 py-3 text-left font-medium">행위</th>
                    <th class="px-6 py-3 text-left font-medium">상세</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                    @php
                        $displayType = match($log->target_type) {
                            '스토리', '방명록' => '기념관',
                            '질문' => 'AI 질문',
                            default => $log->target_type,
                        };
                        $s = $labelStyles[$displayType] ?? $labelStyles['전체'];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 text-gray-500 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-6 py-3 text-[#2c2520]">{{ $log->admin?->user_id ?? '-' }}</td>
                        <td class="px-6 py-3">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold" style="background-color: {{ $s['bg'] }}; color: {{ $s['color'] }};">{{ $displayType }}</span>
                        </td>
                        <td class="px-6 py-3 text-[#2c2520] font-medium">{{ $log->action }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $log->description }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">활동 로그가 없습니다.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-3">
        @forelse($logs as $log)
            @php
                $displayType = match($log->target_type) {
                    '스토리', '방명록' => '기념관',
                    '질문' => 'AI 질문',
                    default => $log->target_type,
                };
                $s = $labelStyles[$displayType] ?? $labelStyles['전체'];
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold" style="background-color: {{ $s['bg'] }}; color: {{ $s['color'] }};">{{ $displayType }}</span>
                    <span class="text-xs text-gray-400">{{ $log->created_at->format('Y-m-d H:i') }}</span>
                </div>
                <p class="text-sm font-medium text-[#2c2520] mb-1">{{ $log->action }}</p>
                <p class="text-sm text-gray-600 mb-2">{{ $log->description }}</p>
                <p class="text-xs text-gray-400">관리자: {{ $log->admin?->user_id ?? '-' }}</p>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center text-gray-400">
                활동 로그가 없습니다.
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($logs->hasPages())
        <div class="mt-6">
            {{ $logs->links() }}
        </div>
    @endif

</x-admin-layout>
