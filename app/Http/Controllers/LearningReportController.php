<?php

namespace App\Http\Controllers;

use App\Services\LearningReportService;
use Illuminate\Http\Request;

class LearningReportController extends Controller
{
    public function index(Request $request, LearningReportService $reportService)
    {
        $filters = $request->validate([
            'attendance' => 'nullable|in:all,not_started,attending,completed',
            'activity' => 'nullable|integer|exists:sessionables,id',
        ]);

        $report = $reportService->build(
            $filters['attendance'] ?? 'all',
            isset($filters['activity']) ? (int) $filters['activity'] : null
        );

        return view('contents.admin.reports.learning', compact('report'));
    }
}
