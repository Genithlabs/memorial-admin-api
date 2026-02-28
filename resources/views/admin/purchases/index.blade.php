<x-admin-layout title="구매 신청 관리">
    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('admin.purchases.index') }}" class="flex gap-3">
            <select name="status" class="border-gray-300 rounded-lg text-sm focus:border-[#c8952e] focus:ring-[#c8952e]">
                <option value="">전체</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>대기</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>승인</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>거절</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-[#c8952e] text-white rounded-lg text-sm font-medium hover:bg-[#b5852a] transition">필터</button>
        </form>
    </div>

    {{-- Desktop Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hidden md:block">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">ID</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">회원</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">상태</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">메모</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">신청일</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">처리일</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">처리</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition" x-data="{ open: false }">
                            <td class="py-3 px-4 text-gray-400">{{ $purchase->id }}</td>
                            <td class="py-3 px-4">
                                @if($purchase->user)
                                    <a href="{{ route('admin.users.show', $purchase->user->id) }}" class="text-[#c8952e] hover:underline">
                                        {{ $purchase->user->user_name }} ({{ $purchase->user->user_id }})
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($purchase->status === 'pending')
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">대기</span>
                                @elseif($purchase->status === 'approved')
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">승인</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">거절</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-500 max-w-xs truncate">{{ $purchase->admin_memo ?? '-' }}</td>
                            <td class="py-3 px-4 text-gray-400">{{ $purchase->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-3 px-4 text-gray-400">{{ $purchase->processed_at ? \Carbon\Carbon::parse($purchase->processed_at)->format('Y-m-d H:i') : '-' }}</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                <button @click="open = !open" class="text-[#c8952e] hover:underline text-sm">처리</button>
                                <form method="POST" action="{{ route('admin.purchases.destroy', $purchase->id) }}"
                                      onsubmit="return confirm('정말 삭제하시겠습니까?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline text-sm">삭제</button>
                                </form>
                                <div x-show="open" x-cloak @click.away="open = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
                                    <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4" @click.stop>
                                        <h3 class="text-base font-semibold text-[#2c2520] mb-4">구매 신청 처리</h3>
                                        <form method="POST" action="{{ route('admin.purchases.updateStatus', $purchase->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <div class="mb-4">
                                                <label class="block text-sm text-gray-700 mb-1">상태</label>
                                                <select name="status" class="w-full border-gray-300 rounded-lg text-sm focus:border-[#c8952e] focus:ring-[#c8952e]">
                                                    <option value="pending" {{ $purchase->status === 'pending' ? 'selected' : '' }}>대기</option>
                                                    <option value="approved" {{ $purchase->status === 'approved' ? 'selected' : '' }}>승인</option>
                                                    <option value="rejected" {{ $purchase->status === 'rejected' ? 'selected' : '' }}>거절</option>
                                                </select>
                                            </div>
                                            <div class="mb-4">
                                                <label class="block text-sm text-gray-700 mb-1">관리자 메모</label>
                                                <textarea name="admin_memo" rows="3"
                                                    class="w-full border-gray-300 rounded-lg text-sm focus:border-[#c8952e] focus:ring-[#c8952e]"
                                                    placeholder="메모 입력...">{{ $purchase->admin_memo }}</textarea>
                                            </div>
                                            <div class="flex justify-end gap-2">
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
                        <tr><td colspan="7" class="py-8 text-center text-gray-400">구매 신청이 없습니다.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-3">
        @forelse($purchases as $purchase)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4" x-data="{ open: false }">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <span class="text-xs text-gray-400">#{{ $purchase->id }}</span>
                        @if($purchase->user)
                            <h4 class="text-sm font-semibold text-gray-900">
                                <a href="{{ route('admin.users.show', $purchase->user->id) }}" class="text-[#c8952e] hover:underline">
                                    {{ $purchase->user->user_name }} ({{ $purchase->user->user_id }})
                                </a>
                            </h4>
                        @else
                            <h4 class="text-sm text-gray-400">-</h4>
                        @endif
                    </div>
                    @if($purchase->status === 'pending')
                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 shrink-0 ml-2">대기</span>
                    @elseif($purchase->status === 'approved')
                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 shrink-0 ml-2">승인</span>
                    @else
                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 shrink-0 ml-2">거절</span>
                    @endif
                </div>
                @if($purchase->admin_memo)
                    <p class="text-xs text-gray-500 mb-2 line-clamp-2">{{ $purchase->admin_memo }}</p>
                @endif
                <div class="text-xs text-gray-400 mb-3">
                    <span>신청: {{ $purchase->created_at->format('Y-m-d H:i') }}</span>
                    @if($purchase->processed_at)
                        <span class="mx-1">·</span>
                        <span>처리: {{ \Carbon\Carbon::parse($purchase->processed_at)->format('Y-m-d H:i') }}</span>
                    @endif
                </div>
                <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                    <button @click="open = !open" class="text-[#c8952e] hover:underline text-sm font-medium">처리</button>
                    <form method="POST" action="{{ route('admin.purchases.destroy', $purchase->id) }}"
                          onsubmit="return confirm('정말 삭제하시겠습니까?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline text-sm">삭제</button>
                    </form>
                    <div x-show="open" x-cloak @click.away="open = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
                        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4" @click.stop>
                            <h3 class="text-base font-semibold text-[#2c2520] mb-4">구매 신청 처리</h3>
                            <form method="POST" action="{{ route('admin.purchases.updateStatus', $purchase->id) }}">
                                @csrf
                                @method('PATCH')
                                <div class="mb-4">
                                    <label class="block text-sm text-gray-700 mb-1">상태</label>
                                    <select name="status" class="w-full border-gray-300 rounded-lg text-sm focus:border-[#c8952e] focus:ring-[#c8952e]">
                                        <option value="pending" {{ $purchase->status === 'pending' ? 'selected' : '' }}>대기</option>
                                        <option value="approved" {{ $purchase->status === 'approved' ? 'selected' : '' }}>승인</option>
                                        <option value="rejected" {{ $purchase->status === 'rejected' ? 'selected' : '' }}>거절</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm text-gray-700 mb-1">관리자 메모</label>
                                    <textarea name="admin_memo" rows="3"
                                        class="w-full border-gray-300 rounded-lg text-sm focus:border-[#c8952e] focus:ring-[#c8952e]"
                                        placeholder="메모 입력...">{{ $purchase->admin_memo }}</textarea>
                                </div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="open = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">취소</button>
                                    <button type="submit" class="px-4 py-2 bg-[#c8952e] text-white rounded-lg text-sm font-medium hover:bg-[#b5852a] transition">저장</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center text-gray-400">구매 신청이 없습니다.</div>
        @endforelse

        @if($purchases->hasPages())
            <div class="mt-4">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
