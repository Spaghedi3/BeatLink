<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Track;
use App\Models\Report;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index()
    {
        // 1. Total number of users
        $totalUsers = User::count();

        // 2. Active users via Chatify's column (assumes 'active_status' = 'online' means active)
        $activeUsers = User::where('updated_at', '>=', now()->subDays(30))->count();

        // 3. Total tracks uploaded
        $totalTracks = Track::count();

        // 4. Report counts by type
        $reportCountsByType = Report::select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        // 5. (Optional) Total reports
        $totalReports = Report::count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'totalTracks',
            'totalReports',
            'reportCountsByType'
        ));
    }
}
