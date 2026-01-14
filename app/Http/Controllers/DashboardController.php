<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard statistics
     */
    public function index(Request $request)
    {
        $user = $request->user();

        # General book counts
        $counts = [
            'total' => $user->books()->count(),
            'read' => $user->books()->where('status', 'read')->count(),
            'reading' => $user->books()->where('status', 'reading')->count(),
            'planning' => $user->books()->where('status', 'planning_to_read')->count(),
        ];

        # Total books in the last 6 months grouped by month
        $monthlyReads = $user->books()
            ->toBase()
            ->where('status', 'read')
            ->whereNotNull('finished_reading_at')
            ->where('finished_reading_at', '>=', Carbon::now()->subMonths(6))
            ->selectRaw("DATE_FORMAT(finished_reading_at, '%Y-%m') as month, DATE_FORMAT(finished_reading_at, '%m/%Y') as label, count(*) as count")
            ->groupBy('month', 'label')
            ->orderBy('month')
            ->get();

        return response()->json([
            'counts' => $counts,
            'monthly_reads' => $monthlyReads
        ]);
    }
}