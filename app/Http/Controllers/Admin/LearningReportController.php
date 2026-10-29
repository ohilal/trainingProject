<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardService;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class LearningReportController extends Controller
{
    /**
     * Redesigned "Learning" admin report:
     * courses, attendees, progress, most viewed / most completed courses,
     * Active Directory department (OU) breakdown and top scoring departments/learners,
     * all visualised with Chart.js.
     */
    public function index(AdminDashboardService $dashboard): View
    {
        $payload = Cache::remember('admin.reports.learning', now()->addMinutes(10), function () use ($dashboard) {
            return [
                'summary'       => $dashboard->summary(),
                'courseStats'   => $dashboard->courseStats(),
                'mostViewed'    => $dashboard->mostViewedCourses(),
                'mostCompleted' => $dashboard->mostCompletedCourses(),
                'departments'   => $dashboard->departmentBreakdown(),
                'topLearners'   => $dashboard->topLearners(),
            ];
        });

        return view('contents.admin.reports.learning', $payload);
    }
}
