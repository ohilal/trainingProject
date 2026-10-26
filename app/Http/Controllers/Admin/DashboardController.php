<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardService;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Admin analytics dashboard:
     * courses, attendees, progress, most viewed / most completed courses,
     * AD department breakdown and top scoring departments/learners.
     */
    public function index(AdminDashboardService $dashboard): View
    {
        $payload = Cache::remember('admin.dashboard.main', now()->addMinutes(10), function () use ($dashboard) {
            return [
                'summary'       => $dashboard->summary(),
                'courseStats'   => $dashboard->courseStats(),
                'mostViewed'    => $dashboard->mostViewedCourses(),
                'mostCompleted' => $dashboard->mostCompletedCourses(),
                'departments'   => $dashboard->departmentBreakdown(),
                'topLearners'   => $dashboard->topLearners(),
            ];
        });

        return view('contents.admin.dashboard.index', $payload);
    }
}
