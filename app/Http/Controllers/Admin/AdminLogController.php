<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use Illuminate\Http\Request;

class AdminLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminLog::with('admin');

        if ($type = $request->input('target_type')) {
            if ($type === '기념관') {
                $query->whereIn('target_type', ['기념관', '스토리', '방명록']);
            } elseif ($type === 'AI 질문') {
                $query->where('target_type', '질문');
            } else {
                $query->where('target_type', $type);
            }
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(30)->withQueryString();

        return view('admin.logs.index', compact('logs'));
    }
}
