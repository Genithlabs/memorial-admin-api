<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Memorial;
use App\Models\PurchaseRequest;
use App\Models\Story;
use App\Models\User;
use App\Models\VisitorComment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_memorials' => Memorial::count(),
            'total_stories' => Story::count(),
            'total_comments' => VisitorComment::count(),
            'total_purchases' => PurchaseRequest::count(),
            'pending_purchases' => PurchaseRequest::where('status', 'pending')->count(),
        ];

        // 최근 7일 가입 추이 (단일 쿼리)
        $startDate = Carbon::today()->subDays(6);
        $signupCounts = User::selectRaw('DATE(created_at) as signup_date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupByRaw('DATE(created_at)')
            ->pluck('count', 'signup_date');

        $dailySignups = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dailySignups[] = [
                'date' => $date->format('m/d'),
                'count' => $signupCounts[$date->toDateString()] ?? 0,
            ];
        }

        $recentUsers = User::orderBy('created_at', 'desc')->limit(5)->get();
        $recentPurchases = PurchaseRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'dailySignups', 'recentUsers', 'recentPurchases'));
    }
}
