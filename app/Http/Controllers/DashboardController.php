<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\ReadingGoal;

class DashboardController extends Controller
{
    /**
     * Display the dashboard statistics
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $currentYear = date('Y');

        # General book counts
        $counts = [
            'total' => $user->books()->count(),
            'read' => $user->books()->read()->count(),
            'reading' => $user->books()->reading()->count(),
            'planning' => $user->books()->planning()->count(),
        ];

        # Total books in the last 6 months grouped by month
        $monthlyReads = $user->books()
            ->read()
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

        # Reading goal for the current year
        $goal = $user->currentYearGoal()->first();
        $readThisYear = $user->books()
            ->read()
            ->whereYear('finished_reading_at', $currentYear)
            ->count();
        $goalData = null;
    
        if ($goal) {
            $percentage = $goal->target > 0 ? ($readThisYear / $goal->target) * 100 : 0;
            
            # Current status based on the day of the year
            $dayOfYear = date('z') + 1;
            $expectedBooks = ($goal->target / 365) * $dayOfYear;
            $status = $readThisYear >= $expectedBooks ? 'on_track' : 'behind';

            $goalData = [
                'target' => $goal->target,
                'read' => $readThisYear,
                'percentage' => round($percentage),
                'status' => $status
            ];
        }

        return response()->json([
            'counts' => $counts,
            'monthly_reads' => $monthlyReads,
            'goal' => $goalData
        ]);
    }

    /**
     * Store or update the reading goal for the current year
     */
    public function storeGoal(Request $request)
    {
        $request->validate(['target' => 'required|integer|min:1']);

        $goal = ReadingGoal::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'year' => date('Y')
            ],
            [
                'target' => $request->target
            ]
        );

        return response()->json($goal);
    }
}