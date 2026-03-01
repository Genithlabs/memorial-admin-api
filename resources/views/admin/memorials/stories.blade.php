<x-admin-layout title="{{ $memorial->name }}의 스토리">
    <div>
        <a href="{{ route('admin.memorials.show', $memorial->id) }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-[#c8952e] mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            기념관 상세
        </a>

        {{-- Desktop Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hidden md:block">
            <div class="p-6 pb-0">
                <h3 class="text-base font-semibold text-[#2c2520] mb-4">{{ $memorial->name }}의 스토리 ({{ $stories->total() }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left py-3 px-4 text-gray-500 font-medium">ID</th>
                            <th class="text-left py-3 px-4 text-gray-500 font-medium">제목</th>
                            <th class="text-left py-3 px-4 text-gray-500 font-medium">작성자</th>
                            <th class="text-left py-3 px-4 text-gray-500 font-medium">노출</th>
                            <th class="text-left py-3 px-4 text-gray-500 font-medium">작성일</th>
                            <th class="text-left py-3 px-4 text-gray-500 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stories as $story)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                                <td class="py-3 px-4 text-gray-400">{{ $story->id }}</td>
                                <td class="py-3 px-4 font-medium text-gray-900 max-w-xs truncate">{{ $story->title ?? '-' }}</td>
                                <td class="py-3 px-4 text-gray-500">{{ $story->user->user_name ?? '-' }}</td>
                                <td class="py-3 px-4">
                                    @if($story->is_visible)
                                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">노출</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">숨김</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-gray-400">{{ $story->created_at->format('Y-m-d') }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2" x-data="{ open: false }">
                                        <button @click="open = true" class="text-[#c8952e] hover:underline text-sm">수정</button>
                                        <form method="POST" action="{{ route('admin.memorials.stories.destroy', [$memorial->id, $story->id]) }}"
                                              onsubmit="return confirm('정말 이 스토리를 삭제하시겠습니까? 사용자가 직접 작성한 콘텐츠이므로 신중하게 처리해 주세요.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:underline text-sm">삭제</button>
                                        </form>

                                        {{-- Edit Modal --}}
                                        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="open = false">
                                            <div class="bg-white rounded-xl shadow-lg w-full max-w-lg mx-4 p-6" @click.stop>
                                                <h3 class="text-base font-semibold text-[#2c2520] mb-4">스토리 수정</h3>
                                                <form method="POST" action="{{ route('admin.memorials.stories.update', [$memorial->id, $story->id]) }}"
                                                      onsubmit="return confirm('스토리를 수정하시겠습니까? 사용자가 직접 작성한 콘텐츠입니다.')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="space-y-4">
                                                        <div>
                                                            <label class="text-xs text-gray-500">제목</label>
                                                            <input type="text" name="title" value="{{ $story->title }}"
                                                                   class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c8952e]/50 focus:border-[#c8952e]">
                                                        </div>
                                                        <div>
                                                            <label class="text-xs text-gray-500">내용</label>
                                                            <textarea name="message" rows="6"
                                                                      class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c8952e]/50 focus:border-[#c8952e]">{{ $story->message }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="flex justify-end gap-2 mt-6">
                                                        <button type="button" @click="open = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">취소</button>
                                                        <button type="submit" class="px-4 py-2 bg-[#c8952e] text-white rounded-lg text-sm font-medium hover:bg-[#b5852a] transition">저장</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-8 text-center text-gray-400">스토리가 없습니다.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($stories->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $stories->links() }}
                </div>
            @endif
        </div>

        {{-- Mobile Cards --}}
        <div class="md:hidden">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-3">
                <h3 class="text-base font-semibold text-[#2c2520]">{{ $memorial->name }}의 스토리 ({{ $stories->total() }})</h3>
            </div>

            <div class="space-y-3">
                @forelse($stories as $story)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4" x-data="{ open: false }">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1 min-w-0">
                                <span class="text-xs text-gray-400">#{{ $story->id }}</span>
                                <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $story->title ?? '-' }}</h4>
                            </div>
                            @if($story->is_visible)
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 ml-2 shrink-0">노출</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 ml-2 shrink-0">숨김</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 mb-3">
                            <span>{{ $story->user->user_name ?? '-' }}</span>
                            <span class="mx-1">&middot;</span>
                            <span>{{ $story->created_at->format('Y-m-d') }}</span>
                        </div>
                        <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                            <button @click="open = true" class="text-[#c8952e] hover:underline text-sm font-medium">수정</button>
                            <form method="POST" action="{{ route('admin.memorials.stories.destroy', [$memorial->id, $story->id]) }}"
                                  onsubmit="return confirm('정말 이 스토리를 삭제하시겠습니까? 사용자가 직접 작성한 콘텐츠이므로 신중하게 처리해 주세요.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline text-sm">삭제</button>
                            </form>
                        </div>

                        {{-- Edit Modal --}}
                        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="open = false">
                            <div class="bg-white rounded-xl shadow-lg w-full max-w-lg mx-4 p-6" @click.stop>
                                <h3 class="text-base font-semibold text-[#2c2520] mb-4">스토리 수정</h3>
                                <form method="POST" action="{{ route('admin.memorials.stories.update', [$memorial->id, $story->id]) }}"
                                      onsubmit="return confirm('스토리를 수정하시겠습니까? 사용자가 직접 작성한 콘텐츠입니다.')">
                                    @csrf
                                    @method('PATCH')
                                    <div class="space-y-4">
                                        <div>
                                            <label class="text-xs text-gray-500">제목</label>
                                            <input type="text" name="title" value="{{ $story->title }}"
                                                   class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c8952e]/50 focus:border-[#c8952e]">
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500">내용</label>
                                            <textarea name="message" rows="6"
                                                      class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c8952e]/50 focus:border-[#c8952e]">{{ $story->message }}</textarea>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2 mt-6">
                                        <button type="button" @click="open = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">취소</button>
                                        <button type="submit" class="px-4 py-2 bg-[#c8952e] text-white rounded-lg text-sm font-medium hover:bg-[#b5852a] transition">저장</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center text-gray-400">스토리가 없습니다.</div>
                @endforelse
            </div>

            @if($stories->hasPages())
                <div class="mt-4">
                    {{ $stories->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
