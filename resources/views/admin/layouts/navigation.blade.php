@php
    $currentRoute = Route::currentRouteName();
@endphp

<aside id="sidebar" class="fixed inset-y-0 left-0 z-20 w-64 bg-[#2c2520] text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out">
    {{-- Logo --}}
    <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
        <div class="w-8 h-8 rounded-full bg-[#c8952e] flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L10 6.2V16h3a1 1 0 110 2H7a1 1 0 110-2h3V6.2L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z"/>
            </svg>
        </div>
        <span class="text-lg font-bold">메모리얼 Admin</span>
    </div>

    {{-- Navigation --}}
    <nav class="mt-4 px-3 space-y-1">
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                  {{ Str::startsWith($currentRoute, 'admin.dashboard') ? 'bg-[#c8952e] text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            대시보드
        </a>

        <a href="{{ route('admin.users.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                  {{ Str::startsWith($currentRoute, 'admin.users') ? 'bg-[#c8952e] text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            회원 관리
        </a>

        <a href="{{ route('admin.memorials.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                  {{ Str::startsWith($currentRoute, 'admin.memorials') ? 'bg-[#c8952e] text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            기념관 관리
        </a>

        <a href="{{ route('admin.purchases.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                  {{ Str::startsWith($currentRoute, 'admin.purchases') ? 'bg-[#c8952e] text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
            </svg>
            구매 신청
        </a>

        <a href="{{ route('admin.questions.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                  {{ Str::startsWith($currentRoute, 'admin.questions') ? 'bg-[#c8952e] text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            질문 관리
        </a>

        <a href="{{ route('admin.logs.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                  {{ Str::startsWith($currentRoute, 'admin.logs') ? 'bg-[#c8952e] text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            활동 로그
        </a>
    </nav>
</aside>

{{-- Mobile overlay --}}
<div onclick="document.getElementById('sidebar').classList.add('-translate-x-full')"
     class="fixed inset-0 bg-black/50 z-10 lg:hidden hidden" id="sidebar-overlay"></div>
