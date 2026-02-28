<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;

class PurchaseRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseRequest::with('user', 'processor');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $purchases = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.purchases.index', compact('purchases'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'admin_memo' => 'nullable|string|max:1000',
        ]);

        $purchase = PurchaseRequest::findOrFail($id);
        $purchase->update([
            'status' => $request->input('status'),
            'admin_memo' => $request->input('admin_memo'),
            'processed_at' => now(),
            'processed_by' => $request->user()->id,
        ]);

        $statusLabels = ['approved' => '승인', 'rejected' => '거절', 'pending' => '대기'];

        AdminLog::log('구매요청 상태 변경', '구매요청', $purchase->id, "구매요청 #{$purchase->id} → {$statusLabels[$request->input('status')]}");

        return redirect()->back()->with('success', "구매 신청이 {$statusLabels[$request->input('status')]}되었습니다.");
    }

    public function destroy($id)
    {
        PurchaseRequest::findOrFail($id)->delete();

        AdminLog::log('구매요청 삭제', '구매요청', $id, "구매요청 #{$id} 삭제");

        return redirect()->route('admin.purchases.index')->with('success', '구매 신청이 삭제되었습니다.');
    }
}
