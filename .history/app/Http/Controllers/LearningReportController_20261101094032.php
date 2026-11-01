<?php

namespace App\Http\Controllers;

use App\Services\LearningReportService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;



class LearningReportController extends Controller
{
    /**
     * Learning report: course attendance, learner progress, activity starts,
     * completions and scores by LDAP OU — with Chart.js visualisations.
     */
    public function index(Request $request): View
    {
        $attendance = (string) $request->query('attendance', 'all');
        $activity   = $request->query('activity') ? (int) $request->query('activity') : null;

        $cacheKey = 'reports.learning.'.$attendance.'.'.($activity ?? 'all');

        $report = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($attendance, $activity) {
            return (new LearningReportService($attendance, $activity))->build();
        });

        return view('contents.admin.reports.learning', ['report' => $report]);
    }
}