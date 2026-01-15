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
            ->where('status', 'read')
            ->whereNotNull('finished_reading_at')
            ->where('finished_reading_at', '>=', Carbon::now()->subMonths(6))
            ->orderBy('finished_reading_at')
            ->get()
            ->groupBy(function($val) {
                return Carbon::parse($val->finished_reading_at)->format('Y-m');
            })
            ->map(function ($group) {
                $date = Carbon::parse($group->first()->finished_reading_at);
                return [
                    'month' => $date->format('Y-m'),
                    'label' => $date->format('m/Y'),
                    'count' => $group->count()
                ];
            })
            ->values();

        return response()->json([
            'counts' => $counts,
            'monthly_reads' => $monthlyReads
        ]);
    }
}