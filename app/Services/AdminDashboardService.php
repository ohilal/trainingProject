<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminDashboardService
{
    /**
     * Learner role id (students). Kept consistent with Participant::scopeLearners().
     */
    protected int $learnerRoleId = 4;

    /**
     * High level counters shown as cards at the top of the dashboard.
     */
    public function summary(): array
    {
        return [
            'courses'          => Course::count(),
            'terms'            => DB::table('terms')->count(),
            'learners'         => $this->learnersQuery()->count(),
            'enrollments'      => DB::table('term_user')->where('role_id', $this->learnerRoleId)->count(),
            'avg_progress'     => round((float) DB::table('workouts')
                                            ->where('is_mentor', false)
                                            ->avg('is_completed') * 100, 1),
            'quiz_attempts'    => DB::table('workout_quiz_logs')
                                            ->whereNotNull('score')
                                            ->where('is_mentor', false)
                                            ->count(),
            'avg_quiz_score'   => round((float) DB::table('workout_quiz_logs')
                                            ->whereNotNull('score')
                                            ->where('is_mentor', false)
                                            ->avg('score'), 1),
        ];
    }

    /**
     * Courses table: attendees, progress %, completion counts, views and avg quiz score.
     */
    public function courseStats(int $limit = 50)
    {
        return $this->allCourseStats()->take($limit);
    }

    /**
     * Cached full course stats (recomputed at most every 10 minutes).
     */
    protected function allCourseStats()
    {
        return Cache::remember('admin.dashboard.courseStats', now()->addMinutes(10), function () {
            return collect($this->computeCourseStats());
        });
    }

    /**
     * MSSQL-safe: aggregate per term, then roll up to courses in PHP instead of
     * GROUP BY-ing non-aggregated columns.
     */
    protected function computeCourseStats(): array
    {
        $rows = DB::table('courses')
            ->leftJoin('terms', 'terms.course_id', '=', 'courses.id')
            ->select('courses.id', 'courses.title', 'terms.id as term_id')
            ->get();

        $termIdsByCourse = [];
        foreach ($rows as $row) {
            if ($row->term_id !== null) {
                $termIdsByCourse[$row->id][] = $row->term_id;
            }
        }

        $allTermIds = array_unique(iterator_to_array(
            (function (array $map) {
                foreach ($map as $ids) {
                    yield from $ids;
                }
            })($termIdsByCourse)
        ));

        $participantsPerTerm  = $this->countsByTerm($allTermIds, fn ($q) => $q);
        $completedPerTerm     = $this->countsByTerm($allTermIds, fn ($q) => $q->where('w.is_completed', 1));
        $totalWorkoutsPerTerm = $this->countsByTerm($allTermIds, fn ($q) => $q);
        $viewsPerTerm         = $this->minutesByTerm($allTermIds);
        $scoresPerTerm        = $this->avgScoresByTerm($allTermIds);

        $stats = [];
        foreach ($termIdsByCourse as $courseId => $termIds) {
            $pick = function (array $map) use ($termIds) {
                $sum = 0;
                foreach ($termIds as $tid) {
                    $sum += $map[$tid] ?? 0;
                }
                return $sum;
            };

            $attendees = $pick($participantsPerTerm);
            $completed = $pick($completedPerTerm);
            $total     = $pick($totalWorkoutsPerTerm);
            $minutes   = $pick($viewsPerTerm);
            $avgScore  = $this->weightedAvg($scoresPerTerm, $termIds);

            $stats[] = [
                'course_id'      => $courseId,
                'title'          => $rows->firstWhere('id', $courseId)->title,
                'attendees'      => $attendees,
                'workouts_total' => $total,
                'workouts_done'  => $completed,
                'progress'       => $total > 0 ? round($completed / $total * 100, 1) : 0,
                'minutes_watched'=> (int) round($minutes),
                'avg_quiz_score' => $avgScore,
            ];
        }

        usort($stats, fn ($a, $b) => $b['attendees'] <=> $a['attendees']);

        return $stats;
    }

    /**
     * Most viewed courses = courses with the most learner activity time.
     */
    public function mostViewedCourses(int $limit = 5)
    {
        return $this->allCourseStats()
            ->sortByDesc('minutes_watched')
            ->take($limit)
            ->values();
    }

    /**
     * Most completed courses = highest completion ratio (min. workouts to qualify).
     */
    public function mostCompletedCourses(int $limit = 5)
    {
        return $this->allCourseStats()
            ->filter(fn ($s) => $s['workouts_total'] >= 3)
            ->sortByDesc('progress')
            ->take($limit)
            ->values();
    }

    /**
     * Departments (Active Directory OUs) the learners belong to,
     * with learner counts and average scores.
     */
    public function departmentBreakdown()
    {
        $learners = $this->learnersQuery()
            ->select('id', 'name', 'username', 'ad_department')
            ->get();

        // workouts have no user_id column; resolve the learner through term_user (participant).
        $quizScores = DB::table('workouts as w')
            ->join('workout_quiz_logs as wql', 'wql.workout_id', '=', 'w.id')
            ->join('term_user as tu', 'tu.id', '=', 'w.participant_id')
            ->where('w.is_mentor', false)
            ->whereNotNull('wql.score')
            ->select('tu.user_id', DB::raw('AVG(wql.score * 1.0) as avg_score'))
            ->groupBy('tu.user_id')
            ->pluck('avg_score', 'user_id');

        $completions = DB::table('workouts as w')
            ->join('term_user as tu', 'tu.id', '=', 'w.participant_id')
            ->where('w.is_mentor', false)
            ->selectRaw('tu.user_id as user_id, COUNT(*) as total, SUM(CASE WHEN w.is_completed = 1 THEN 1 ELSE 0 END) as done')
            ->groupBy('tu.user_id')
            ->get()
            ->keyBy('user_id');

        $byDepartment = [];
        foreach ($learners as $learner) {
            $dept = $learner->ad_department ?: 'غير محدد';

            $done  = $completions[$learner->id]->done   ?? 0;
            $total = $completions[$learner->id]->total ?? 0;
            $score = isset($quizScores[$learner->id]) ? (float) $quizScores[$learner->id] : null;

            $byDepartment[$dept]['learners']          = ($byDepartment[$dept]['learners'] ?? 0) + 1;
            $byDepartment[$dept]['completed']         = ($byDepartment[$dept]['completed'] ?? 0) + (int) $done;
            $byDepartment[$dept]['workouts']          = ($byDepartment[$dept]['workouts'] ?? 0) + (int) $total;
            $byDepartment[$dept]['score_sum'][]       = $score;

            $byDepartment[$dept]['top_learners'][] = [
                'name'  => $learner->name,
                'score' => $score,
            ];
        }

        $departments = collect($byDepartment)->map(function ($data, $name) {
            $scores = array_filter($data['score_sum'], fn ($s) => $s !== null);
            $top = collect($data['top_learners'])
                ->filter(fn ($l) => $l['score'] !== null)
                ->sortByDesc('score')
                ->take(3)
                ->values();

            return [
                'department' => $name,
                'learners'   => $data['learners'],
                'progress'   => $data['workouts'] > 0 ? round($data['completed'] / $data['workouts'] * 100, 1) : 0,
                'avg_score'  => count($scores) ? round(array_sum($scores) / count($scores), 1) : null,
                'top_learners' => $top,
            ];
        })->sortByDesc('avg_score')->values();

        return $departments;
    }

    /**
     * Top scoring individual learners across quizzes.
     */
    public function topLearners(int $limit = 10)
    {
        $scores = DB::table('workouts as w')
            ->join('workout_quiz_logs as wql', 'wql.workout_id', '=', 'w.id')
            ->join('term_user as tu', 'tu.id', '=', 'w.participant_id')
            ->where('w.is_mentor', false)
            ->whereNotNull('wql.score')
            ->select('tu.user_id', DB::raw('AVG(wql.score * 1.0) as avg_score'), DB::raw('COUNT(*) as attempts'))
            ->groupBy('tu.user_id')
            ->orderByDesc('avg_score')
            ->get();

        $users = User::whereIn('id', $scores->pluck('user_id'))->get()->keyBy('id');

        return $scores->map(function ($row) use ($users) {
            return [
                'name'        => $users[$row->user_id]->name ?? ('#' . $row->user_id),
                'department'  => $users[$row->user_id]->ad_department ?? '-',
                'avg_score'   => round((float) $row->avg_score, 1),
                'attempts'    => (int) $row->attempts,
            ];
        })->take($limit);
    }

    protected function learnersQuery()
    {
        return User::query()
            ->whereHas('roles', fn ($roles) => $roles->where('roles.id', $this->learnerRoleId));
    }

    /**
     * Per-term workout counts (workouts joined through term_user participants).
     */
    protected function countsByTerm(array $termIds, callable $modifier): array
    {
        if (!$termIds) {
            return [];
        }

        $query = DB::table('workouts as w')
            ->join('term_user as tu', 'tu.id', '=', 'w.participant_id')
            ->where('w.is_mentor', false)
            ->whereIn('tu.term_id', $termIds);

        $query = $modifier($query) ?? $query;

        return $query->select('tu.term_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('tu.term_id')
            ->pluck('cnt', 'term_id')
            ->toArray();
    }

    /**
     * Total minutes of learner activity per term (MSSQL DATEDIFF on view timestamps).
     */
    protected function minutesByTerm(array $termIds): array
    {
        if (!$termIds) {
            return [];
        }

        return DB::table('workouts as w')
            ->join('term_user as tu', 'tu.id', '=', 'w.participant_id')
            ->where('w.is_mentor', false)
            ->whereNotNull('w.date_last_view')
            ->whereIn('tu.term_id', $termIds)
            ->select('tu.term_id', DB::raw('SUM(DATEDIFF(minute, w.date_first_view, w.date_last_view)) as cnt'))
            ->groupBy('tu.term_id')
            ->pluck('cnt', 'term_id')
            ->map(fn ($v) => (float) $v)
            ->toArray();
    }

    protected function avgScoresByTerm(array $termIds): array
    {
        if (!$termIds) {
            return [];
        }

        $rows = DB::table('workouts as w')
            ->join('term_user as tu', 'tu.id', '=', 'w.participant_id')
            ->join('workout_quiz_logs as wql', 'wql.workout_id', '=', 'w.id')
            ->where('w.is_mentor', false)
            ->whereNotNull('wql.score')
            ->whereIn('tu.term_id', $termIds)
            ->select('tu.term_id', DB::raw('AVG(wql.score * 1.0) as avg_score'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('tu.term_id')
            ->get();

        $out = [];
        foreach ($rows as $row) {
            $out[$row->term_id] = ['avg' => (float) $row->avg_score, 'cnt' => (int) $row->cnt];
        }
        return $out;
    }

    protected function weightedAvg(array $scoresPerTerm, array $termIds): ?float
    {
        $sum = 0;
        $cnt = 0;
        foreach ($termIds as $tid) {
            if (isset($scoresPerTerm[$tid])) {
                $sum += $scoresPerTerm[$tid]['avg'] * $scoresPerTerm[$tid]['cnt'];
                $cnt += $scoresPerTerm[$tid]['cnt'];
            }
        }

        return $cnt > 0 ? round($sum / $cnt, 1) : null;
    }
}
