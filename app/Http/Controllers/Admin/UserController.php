<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Memorial;
use App\Models\Story;
use App\Models\User;
use App\Models\AdminLog;
use App\Models\VisitorComment;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // 슈퍼관리자(admin)는 본인 로그인 시에만 목록에 표시
        if (auth()->user()->user_id !== 'admin') {
            $query->where('user_id', '!=', 'admin');
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('user_id', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->input('filter') === 'admin') {
            $query->where('is_admin', 1);
        } elseif ($request->input('filter') === 'normal') {
            $query->where('is_admin', 0);
        } elseif ($request->input('filter') === 'dormancy') {
            $query->where('is_dormancy', 1);
        } elseif ($request->input('filter') === 'withdraw') {
            $query->where('is_withdraw', 1);
        } elseif ($request->input('filter') === 'trial') {
            $query->where('is_trial', 1);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::with('memorial', 'purchaseRequests')->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // 기본 정보 수정
        if ($request->has('user_name')) {
            $request->validate([
                'user_name' => 'required|string|max:50',
                'user_id' => "required|string|max:50|unique:mm_users,user_id,{$user->id}",
                'email' => "required|email|max:100|unique:mm_users,email,{$user->id}",
            ]);

            $changes = [];
            if ($user->user_name !== $request->input('user_name')) {
                $changes[] = "이름: {$user->user_name} → {$request->input('user_name')}";
                $user->user_name = $request->input('user_name');
            }
            if ($user->user_id !== $request->input('user_id')) {
                $changes[] = "아이디: {$user->user_id} → {$request->input('user_id')}";
                $user->user_id = $request->input('user_id');
            }
            if ($user->email !== $request->input('email')) {
                $changes[] = "이메일: {$user->email} → {$request->input('email')}";
                $user->email = $request->input('email');
            }

            $user->save();

            if ($changes) {
                AdminLog::log('회원 정보 수정', '회원', $user->id, implode(', ', $changes));
            }

            return redirect()->back()->with('success', '회원 정보가 수정되었습니다.');
        }

        // 상태 관리
        if ($request->has('is_admin')) {
            $user->is_admin = $request->boolean('is_admin') ? 1 : 0;
        }
        if ($request->has('is_dormancy')) {
            $user->is_dormancy = $request->boolean('is_dormancy') ? 1 : 0;
        }
        if ($request->has('is_withdraw')) {
            $user->is_withdraw = $request->boolean('is_withdraw') ? 1 : 0;
        }

        $user->save();

        AdminLog::log('회원 상태 수정', '회원', $user->id, "{$user->user_id} 회원 상태 수정");

        return redirect()->back()->with('success', '회원 상태가 수정되었습니다.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // 슈퍼관리자 삭제 방지
        if ($user->user_id === 'admin') {
            return redirect()->back()->with('error', '슈퍼관리자는 삭제할 수 없습니다.');
        }

        // 관련 데이터 삭제 (기념관 → 스토리/방명록)
        $memorial = Memorial::where('user_id', $user->id)->first();
        if ($memorial) {
            Story::where('memorial_id', $memorial->id)->delete();
            VisitorComment::where('memorial_id', $memorial->id)->delete();
            $memorial->delete();
        }

        // 회원이 작성한 스토리/방명록도 삭제
        Story::where('user_id', $user->id)->delete();
        VisitorComment::where('user_id', $user->id)->delete();

        AdminLog::log('회원 삭제', '회원', $user->id, "{$user->user_id} 회원 삭제");

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', '회원이 삭제되었습니다.');
    }
}
