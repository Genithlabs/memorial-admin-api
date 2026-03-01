<x-guest-layout>
    <div class="text-center mb-6">
        <div class="w-12 h-12 rounded-full bg-[#c8952e] flex items-center justify-center mx-auto mb-3">
            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L10 6.2V16h3a1 1 0 110 2H7a1 1 0 110-2h3V6.2L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z"/>
            </svg>
        </div>
        <h2 class="text-lg font-bold text-[#2c2520]">메모리얼 Admin</h2>
        <p class="text-sm text-gray-500 mt-1">관리자 로그인</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- User ID -->
        <div>
            <x-input-label for="user_id" :value="__('아이디')" />
            <x-text-input id="user_id" class="block mt-1 w-full" type="text" name="user_id" :value="old('user_id')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('비밀번호')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#c8952e] shadow-sm focus:ring-[#c8952e]" name="remember">
                <span class="ml-2 text-sm text-gray-600">로그인 유지</span>
            </label>
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full px-4 py-2.5 bg-[#c8952e] text-white rounded-lg text-sm font-semibold hover:bg-[#b5852a] focus:outline-none focus:ring-2 focus:ring-[#c8952e] focus:ring-offset-2 transition">
                로그인
            </button>
        </div>
    </form>
</x-guest-layout>
