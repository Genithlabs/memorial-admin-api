<x-admin-layout title="기념관 상세">
    @push('head')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=gowun-dodum:400&family=ibm-plex-sans:400,700&display=swap">
    @endpush
    @php
        // 콘텐츠 포맷 감지 (앱의 detectFormat 로직 동일)
        $careerContents = $memorial->career_contents ?? '';
        $contentFormat = 'text';
        if ($careerContents) {
            if (preg_match('/<[a-z][\s\S]*>/i', $careerContents)) {
                $contentFormat = 'html';
            } else {
                $mdPatterns = ['/^#{1,6}\s/m', '/\*\*[^*]+\*\*/', '/^[-*]\s/m', '/^\d+\.\s/m', '/\[.+\]\(.+\)/', '/^>/m', '/^---$/m'];
                $distinctCount = 0;
                $hasRepeat = false;
                foreach ($mdPatterns as $p) {
                    $matches = preg_match_all($p, $careerContents);
                    if ($matches > 0) {
                        $distinctCount++;
                        if ($matches >= 2) $hasRepeat = true;
                    }
                }
                if ($distinctCount >= 2 || $hasRepeat) $contentFormat = 'markdown';
            }
        }
    @endphp
    <div>
        <a href="{{ route('admin.memorials.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-[#c8952e] mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            기념관 목록
        </a>

        {{-- Notice --}}
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.168 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
            </svg>
            <div class="text-sm text-amber-800">
                <p class="font-medium">기념관 정보 변경 시 주의사항</p>
                <p class="mt-1 text-amber-700">기념관은 사용자가 소중한 사람을 기리기 위해 직접 작성한 공간입니다. 설정 변경 및 삭제 시 신중하게 처리해 주세요.</p>
            </div>
        </div>

        {{-- Memorial Info (read-only) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="text-base font-semibold text-[#2c2520] mb-5">기본 정보</h3>
            <dl class="rounded-lg border border-gray-200 overflow-hidden">
                {{-- Row 1: 기념관명 / 소유자 --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 border-b border-gray-200 bg-gray-50">
                    <div class="px-4 py-3 sm:border-r sm:border-gray-200">
                        <dt class="text-xs font-medium text-gray-500">기념관명</dt>
                        <dd class="mt-1 text-sm font-semibold text-[#2c2520]">{{ $memorial->name }}</dd>
                    </div>
                    <div class="px-4 py-3 border-t sm:border-t-0 border-gray-200">
                        <dt class="text-xs font-medium text-gray-500">소유자</dt>
                        <dd class="mt-1 text-sm font-semibold text-[#2c2520]">
                            @if($owner)
                                <a href="{{ route('admin.users.show', $owner->id) }}" class="text-[#c8952e] hover:underline">
                                    {{ $owner->user_name }} ({{ $owner->user_id }})
                                </a>
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                </div>
                {{-- Row 2: 출생 / 서거 --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 border-b border-gray-200">
                    <div class="px-4 py-3 sm:border-r sm:border-gray-200">
                        <dt class="text-xs font-medium text-gray-500">출생</dt>
                        <dd class="mt-1 text-sm font-semibold text-[#2c2520]">{{ $memorial->birth_start ?? '-' }}</dd>
                    </div>
                    <div class="px-4 py-3 border-t sm:border-t-0 border-gray-200">
                        <dt class="text-xs font-medium text-gray-500">서거</dt>
                        <dd class="mt-1 text-sm font-semibold text-[#2c2520]">{{ $memorial->birth_end ?? '-' }}</dd>
                    </div>
                </div>
                {{-- Row 3: 생성일 / 공개 상태 --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 {{ $memorial->career_contents ? 'border-b border-gray-200' : '' }} bg-gray-50">
                    <div class="px-4 py-3 sm:border-r sm:border-gray-200">
                        <dt class="text-xs font-medium text-gray-500">생성일</dt>
                        <dd class="mt-1 text-sm font-semibold text-[#2c2520]">{{ $memorial->created_at->format('Y-m-d H:i') }}</dd>
                    </div>
                    <div class="px-4 py-3 border-t sm:border-t-0 border-gray-200">
                        <dt class="text-xs font-medium text-gray-500">공개 상태</dt>
                        <dd class="mt-1">
                            @if($memorial->is_open)
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">공개</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-600">비공개</span>
                            @endif
                        </dd>
                    </div>
                </div>
                {{-- Row 4: 생애 --}}
                @if($memorial->career_contents)
                <div class="px-4 py-4">
                    <dt class="text-xs font-medium text-gray-500 mb-2">생애</dt>
                    <dd class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $memorial->career_contents }}</dd>
                    <div class="mt-3">
                        <span class="format-label format-label--{{ $contentFormat }}">
                            @if($contentFormat === 'html')
                                <svg viewBox="0 0 16 16" fill="currentColor"><path d="M0 8v8h5v-4h6v4h5V8L8 0z"/></svg>
                            @elseif($contentFormat === 'markdown')
                                <svg viewBox="0 0 16 16" fill="currentColor"><path d="M14.85 3H1.15C.52 3 0 3.52 0 4.15v7.7C0 12.48.52 13 1.15 13h13.7c.63 0 1.15-.52 1.15-1.15v-7.7C16 3.52 15.48 3 14.85 3zM9 11H7V8L5.5 9.92 4 8v3H2V5h2l1.5 2L7 5h2v6zm2.99.5L9.5 8H11V5h2v3h1.5l-2.51 3.5z"/></svg>
                            @else
                                <svg viewBox="0 0 16 16" fill="currentColor"><path d="M2 3.75A.75.75 0 012.75 3h10.5a.75.75 0 010 1.5H2.75A.75.75 0 012 3.75zm0 4A.75.75 0 012.75 7h10.5a.75.75 0 010 1.5H2.75A.75.75 0 012 7.75zm0 4a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75z"/></svg>
                            @endif
                            Raw {{ strtoupper($contentFormat) }}
                        </span>
                    </div>
                </div>
                @endif
            </dl>
        </div>

        {{-- Profile Image & BGM --}}
        @if($memorial->attachmentProfileImage || $memorial->attachmentBgm)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="text-base font-semibold text-[#2c2520] mb-4">미디어</h3>
            <div class="flex flex-wrap items-start gap-6">
                @if($memorial->attachmentProfileImage)
                    <div>
                        <p class="text-xs text-gray-500 mb-2">프로필 이미지</p>
                        <img src="{{ $memorial->attachmentProfileImage->file_path }}" alt="프로필"
                             class="w-24 h-24 object-cover rounded-lg">
                    </div>
                @endif
                @if($memorial->attachmentBgm)
                    <div class="flex-1 min-w-[200px]">
                        <p class="text-xs text-gray-500 mb-2">배경음악</p>
                        <audio controls class="w-full">
                            <source src="{{ $memorial->attachmentBgm->file_path }}">
                        </audio>
                        <p class="text-xs text-gray-400 mt-1">{{ $memorial->attachmentBgm->file_name }}</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Visibility Toggle --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="text-base font-semibold text-[#2c2520] mb-4">공개 설정</h3>
            <form method="POST" action="{{ route('admin.memorials.update', $memorial->id) }}"
                  onsubmit="return confirm('기념관 공개 설정을 변경하시겠습니까?')">
                @csrf
                @method('PATCH')
                <label class="flex items-center gap-3">
                    <input type="hidden" name="is_open" value="0">
                    <input type="checkbox" name="is_open" value="1" {{ $memorial->is_open ? 'checked' : '' }}
                           class="rounded border-gray-300 text-[#c8952e] focus:ring-[#c8952e]">
                    <span class="text-sm text-gray-700">공개</span>
                </label>
                <div class="mt-4">
                    <button type="submit" class="px-4 py-2 bg-[#c8952e] text-white rounded-lg text-sm font-medium hover:bg-[#b5852a] transition">저장</button>
                </div>
            </form>
        </div>

        {{-- Edit & Delete --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-base font-semibold text-[#2c2520] mb-2">기념관 정보 수정</h3>
                <p class="text-sm text-gray-500 mb-4">기념관명, 출생/서거일, 생애 등 기본 정보를 수정합니다.</p>
                <a href="{{ route('admin.memorials.edit', $memorial->id) }}"
                   class="inline-flex items-center gap-1 px-4 py-2 bg-[#c8952e] text-white rounded-lg text-sm font-medium hover:bg-[#b5852a] transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    정보 수정
                </a>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-red-100 p-6">
                <h3 class="text-base font-semibold text-red-600 mb-2">기념관 삭제</h3>
                <p class="text-sm text-gray-500 mb-4">기념관을 삭제하면 관련 스토리, 방명록이 모두 함께 삭제되며 복구할 수 없습니다.</p>
                <form method="POST" action="{{ route('admin.memorials.destroy', $memorial->id) }}"
                      onsubmit="return confirm('정말 이 기념관을 삭제하시겠습니까? 스토리, 방명록이 모두 삭제됩니다.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition">기념관 삭제</button>
                </form>
            </div>
        </div>

        {{-- Stories & Comments Summary --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="text-base font-semibold text-[#2c2520] mb-4">콘텐츠</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <span class="text-sm text-gray-500">스토리</span>
                        <span class="ml-2 text-lg font-semibold text-[#2c2520]">{{ $storyCount }}건</span>
                    </div>
                    <a href="{{ route('admin.memorials.stories.index', $memorial->id) }}" class="text-[#c8952e] hover:underline text-sm font-medium">관리 &rarr;</a>
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <span class="text-sm text-gray-500">방명록</span>
                        <span class="ml-2 text-lg font-semibold text-[#2c2520]">{{ $commentCount }}건</span>
                    </div>
                    <a href="{{ route('admin.memorials.comments.index', $memorial->id) }}" class="text-[#c8952e] hover:underline text-sm font-medium">관리 &rarr;</a>
                </div>
            </div>
        </div>
        {{-- Platform Previews --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <h3 class="text-base font-semibold text-[#2c2520]">플랫폼별 미리보기</h3>
            </div>

            @php
                $profileUrl = $memorial->attachmentProfileImage?->file_path;
                $birthStart = $memorial->birth_start ? date('Y.m.d', strtotime($memorial->birth_start)) : '';
                $birthEnd = $memorial->birth_end ? date('Y.m.d', strtotime($memorial->birth_end)) : '';
                $lifespanText = '';
                if ($birthStart && $birthEnd) $lifespanText = "{$birthStart} ~ {$birthEnd}";
                elseif ($birthStart) $lifespanText = "{$birthStart} ~";
                elseif ($birthEnd) $lifespanText = "~ {$birthEnd}";

            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Web (PC) Preview --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span class="text-sm font-medium text-gray-500">웹 (PC)</span>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-md">
                        {{-- Browser Chrome --}}
                        <div class="bg-gray-100 border-b border-gray-200 px-4 py-2 flex items-center gap-2">
                            <div class="flex gap-1.5">
                                <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                <div class="w-3 h-3 rounded-full bg-green-400"></div>
                            </div>
                            <div class="flex-1 bg-white rounded px-3 py-1 text-xs text-gray-400 ml-2 truncate">yourmemorial.kr/detail/{{ $memorial->id }}</div>
                        </div>

                        {{-- Web Header --}}
                        <div class="bg-white border-b border-[#ececee] px-4 py-2 flex items-center gap-2" style="font-family: 'Gowun Dodum', sans-serif;">
                            <img src="https://yourmemorial.kr/logo.svg" alt="Logo" class="w-5 h-5" onerror="this.style.display='none'">
                            <span class="text-sm font-semibold text-black">메모리얼</span>
                        </div>

                        {{-- Background Image --}}
                        <div style="height: 180px;">
                            <div class="w-full h-full bg-cover bg-center" style="background-image: url('https://yourmemorial.kr/form-top-bg.jpeg');"></div>
                        </div>

                        {{-- Profile --}}
                        <div class="flex justify-center -mt-12 relative z-10">
                            @if($profileUrl)
                                <img src="{{ $profileUrl }}" alt="프로필" class="w-24 h-24 object-cover rounded-lg shadow-lg bg-gray-200">
                            @else
                                <div class="w-24 h-24 rounded-lg bg-gray-200 shadow-lg flex items-center justify-center">
                                    <svg class="w-10 h-10 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                </div>
                            @endif
                        </div>

                        {{-- Name & Lifespan --}}
                        <div class="text-center py-5" style="font-family: 'Gowun Dodum', sans-serif;">
                            <h2 class="text-2xl font-bold text-black">{{ $memorial->name }}</h2>
                            @if($lifespanText)
                                <p class="text-base text-black/70 mt-1">{{ $lifespanText }}</p>
                            @endif
                        </div>

                        {{-- Tabs & Bio (same width container) --}}
                        <div class="mx-auto" style="width: 70%; font-family: 'IBM Plex Sans', sans-serif;">
                            {{-- Tabs --}}
                            <div class="flex justify-center gap-4 mb-4" style="border-radius: 12px;">
                                <div class="text-center py-2 px-3 text-sm font-bold rounded-lg" style="width: 80%; background-color: #000; color: #fff; box-shadow: 0px 4px 6px rgba(0,0,0,0.2);">생애<br><span style="font-weight: normal; font-size: 0.75rem;">Life</span></div>
                                <div class="text-center py-2 px-3 text-sm font-bold rounded-lg" style="width: 80%; background-color: #f3f3f3; color: #000; box-shadow: 0px 4px 6px rgba(0,0,0,0.2);">기억들<br><span style="font-weight: normal; font-size: 0.75rem;">Memories</span></div>
                            </div>

                            {{-- Bio (포맷별 렌더링) --}}
                            <div style="margin-top: 2rem;">
                                <div style="background-color: rgb(247 247 247 / 95%); border-radius: 4px; padding: 1rem;">
                                    @if($contentFormat === 'html')
                                        <div class="web-bio-html" style="font-size: 0.875rem; line-height: 1.5; color: #000; white-space: pre-wrap; word-wrap: break-word;">{!! $careerContents !!}</div>
                                    @elseif($contentFormat === 'markdown')
                                        <div id="web-bio-markdown" class="web-bio-md" style="font-size: 0.875rem; line-height: 1.5; color: #000; word-wrap: break-word;"></div>
                                    @else
                                        <div style="font-size: 0.875rem; line-height: 1.5; color: #000; white-space: pre-wrap; word-wrap: break-word;">{{ $careerContents ?: '(내용 없음)' }}</div>
                                    @endif
                                </div>
                                <div style="margin-top: 8px;">
                                    <span class="format-label format-label--{{ $contentFormat }}">
                                        @if($contentFormat === 'html')
                                            <svg viewBox="0 0 16 16" fill="currentColor"><path d="M0 8v8h5v-4h6v4h5V8L8 0z"/></svg>
                                        @elseif($contentFormat === 'markdown')
                                            <svg viewBox="0 0 16 16" fill="currentColor"><path d="M14.85 3H1.15C.52 3 0 3.52 0 4.15v7.7C0 12.48.52 13 1.15 13h13.7c.63 0 1.15-.52 1.15-1.15v-7.7C16 3.52 15.48 3 14.85 3zM9 11H7V8L5.5 9.92 4 8v3H2V5h2l1.5 2L7 5h2v6zm2.99.5L9.5 8H11V5h2v3h1.5l-2.51 3.5z"/></svg>
                                        @else
                                            <svg viewBox="0 0 16 16" fill="currentColor"><path d="M2 3.75A.75.75 0 012.75 3h10.5a.75.75 0 010 1.5H2.75A.75.75 0 012 3.75zm0 4A.75.75 0 012.75 7h10.5a.75.75 0 010 1.5H2.75A.75.75 0 012 7.75zm0 4a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75z"/></svg>
                                        @endif
                                        {{ strtoupper($contentFormat) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div style="height: 2rem;"></div>
                    </div>
                </div>

                {{-- App Preview --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span class="text-sm font-medium text-gray-500">앱</span>
                    </div>
                    <div class="mx-auto" style="max-width: 375px;">
                        <div class="bg-[#faf8f5] rounded-[2rem] border-[3px] border-gray-800 overflow-hidden shadow-xl" style="min-height: 580px;">
                            {{-- Status Bar --}}
                            <div class="bg-gray-800 text-white text-center text-xs py-1.5 font-medium">미리보기</div>

                            {{-- Profile Header --}}
                            <div class="relative">
                                <div class="h-[140px] bg-cover bg-center" style="background-image: url('https://yourmemorial.kr/form-top-bg.jpeg');">
                                    <div class="absolute inset-0 bg-black/15"></div>
                                </div>
                                <div class="flex flex-col items-center -mt-[50px] relative z-10 pb-4">
                                    @if($profileUrl)
                                        <img src="{{ $profileUrl }}" alt="프로필" class="w-[100px] h-[100px] rounded-full border-[3px] border-white object-cover shadow-lg bg-gray-200">
                                    @else
                                        <div class="w-[100px] h-[100px] rounded-full border-[3px] border-white bg-gray-200 shadow-lg flex items-center justify-center">
                                            <svg class="w-10 h-10 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                        </div>
                                    @endif
                                    <h2 class="mt-3 text-xl font-bold text-[#2c2520] text-center">{{ $memorial->name }}</h2>
                                    @if($lifespanText)
                                        <p class="text-sm text-gray-500 mt-1 text-center">{{ $lifespanText }}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Tab Bar --}}
                            <div class="flex border-b border-gray-200 px-4">
                                <div class="flex-1 text-center py-2.5 text-sm font-semibold text-[#c8952e] border-b-2 border-[#c8952e]">생애</div>
                                <div class="flex-1 text-center py-2.5 text-sm text-gray-400">기억들</div>
                            </div>

                            {{-- Bio (앱 detectFormat 로직 적용) --}}
                            <div class="px-4 py-4">
                                <h3 class="text-base font-bold text-[#2c2520] mb-3">생애</h3>
                                @if($contentFormat === 'html')
                                    <div class="app-bio-html" style="color: #2c2520; font-size: 15px; line-height: 26px; word-wrap: break-word;">{!! $careerContents !!}</div>
                                @elseif($contentFormat === 'markdown')
                                    <div id="app-bio-markdown" class="app-bio-md" style="color: #2c2520; font-size: 15px; line-height: 26px; word-wrap: break-word;"></div>
                                @else
                                    <div style="color: #2c2520; font-size: 15px; line-height: 26px; white-space: pre-line; word-wrap: break-word;">{{ $careerContents ?: '(내용 없음)' }}</div>
                                @endif
                                <div class="mt-2">
                                    <span class="format-label format-label--{{ $contentFormat }}">
                                        @if($contentFormat === 'html')
                                            <svg viewBox="0 0 16 16" fill="currentColor"><path d="M0 8v8h5v-4h6v4h5V8L8 0z"/></svg>
                                        @elseif($contentFormat === 'markdown')
                                            <svg viewBox="0 0 16 16" fill="currentColor"><path d="M14.85 3H1.15C.52 3 0 3.52 0 4.15v7.7C0 12.48.52 13 1.15 13h13.7c.63 0 1.15-.52 1.15-1.15v-7.7C16 3.52 15.48 3 14.85 3zM9 11H7V8L5.5 9.92 4 8v3H2V5h2l1.5 2L7 5h2v6zm2.99.5L9.5 8H11V5h2v3h1.5l-2.51 3.5z"/></svg>
                                        @else
                                            <svg viewBox="0 0 16 16" fill="currentColor"><path d="M2 3.75A.75.75 0 012.75 3h10.5a.75.75 0 010 1.5H2.75A.75.75 0 012 3.75zm0 4A.75.75 0 012.75 7h10.5a.75.75 0 010 1.5H2.75A.75.75 0 012 7.75zm0 4a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75z"/></svg>
                                        @endif
                                        {{ strtoupper($contentFormat) }}
                                    </span>
                                </div>
                            </div>
                            <div style="height: 2rem;"></div>
                        </div>
                    </div>
                </div>

            </div>
            <p class="text-center text-xs text-gray-400 mt-3">미리보기는 실제 화면의 근사치입니다.</p>
        </div>
    </div>

    {{-- GitHub 스타일 라벨 + 앱 미리보기 스타일 --}}
    <style>
        .format-label { display: inline-flex; align-items: center; gap: 4px; padding: 2px 10px; font-size: 12px; font-weight: 500; line-height: 22px; border-radius: 9999px; white-space: nowrap; }
        .format-label svg { width: 12px; height: 12px; }
        .format-label--html { background-color: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .format-label--markdown { background-color: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; }
        .format-label--text { background-color: #f9fafb; color: #4b5563; border: 1px solid #e5e7eb; }
        .app-bio-html p { margin-top: 0; margin-bottom: 8px; }
        .app-bio-html a { color: #c8952e; }
        .app-bio-md h2 { font-size: 18px; font-weight: 700; color: #2c2520; margin-top: 16px; margin-bottom: 8px; }
        .app-bio-md h3 { font-size: 16px; font-weight: 600; color: #2c2520; margin-top: 12px; margin-bottom: 6px; }
        .app-bio-md p { margin-top: 0; margin-bottom: 8px; }
        .app-bio-md strong { font-weight: 700; }
        .app-bio-md a { color: #c8952e; }
        .app-bio-md hr { background-color: #e5e5e5; height: 1px; border: none; margin: 12px 0; }
        .app-bio-md blockquote { background-color: #f5f5f5; border-left: 3px solid #c8952e; padding: 4px 12px; margin: 8px 0; }
        .web-bio-html p { margin-top: 0; margin-bottom: 8px; }
        .web-bio-md h2 { font-size: 1rem; font-weight: 700; margin-top: 16px; margin-bottom: 8px; }
        .web-bio-md h3 { font-size: 0.9375rem; font-weight: 600; margin-top: 12px; margin-bottom: 6px; }
        .web-bio-md p { margin-top: 0; margin-bottom: 8px; }
        .web-bio-md strong { font-weight: 700; }
        .web-bio-md a { color: #c8952e; }
        .web-bio-md hr { background-color: #e5e5e5; height: 1px; border: none; margin: 12px 0; }
        .web-bio-md blockquote { background-color: #f0f0f0; border-left: 3px solid #999; padding: 4px 12px; margin: 8px 0; }
    </style>

    @if($contentFormat === 'markdown')
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var raw = @json($careerContents);
            var webEl = document.getElementById('web-bio-markdown');
            var appEl = document.getElementById('app-bio-markdown');
            if (typeof marked !== 'undefined') {
                if (webEl) webEl.innerHTML = marked.parse(raw);
                if (appEl) appEl.innerHTML = marked.parse(raw);
            }
        });
    </script>
    @endif
</x-admin-layout>
