<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Builds the data array consumed by contents/admin/reports/learning.blade.php.
 *
 * Schema mapping (this platform):
 *  - "Course"      -> courses (+ terms: course_terms table is named `terms`)
 *  - "Activity"    -> a session attached to a term via session_term, made
 *                    learnable through sessionables; learner progress over an
 *                    activity is tracked in `workouts` (one row per
 *                    participant+sessionable view).
 *  - "Attendance"  -> derived from workouts.is_completed / existence of rows
 *                    for every sessionable of the selected activities.
 *  - "LDAP OU"     -> users.ad_department (parsed from the AD distinguished
 *                    name at LDAP sign-in).
 *  - "Quiz score"  -> workout_quiz_logs.score averaged per learner.
 *
 * All queries are MSSQL-compatible: no LIMIT (top-N done collection-side),
 * deterministic ordering, no GROUP BY alias references.
 */
class LearningReportService
{
    /** Learner role id (kept consistent with AdminDashboardService). */
    protected int $learnerRoleId = 4;

    /** @var string all|not_started|attending|completed */
    protected string $attendanceFilter;

    /** @var int|null selected activity (session) id */
    protected ?int $selectedActivity;

    public function __construct(?string $attendanceFilter = 'all', ?int $selectedActivity = null)
    {
        $allowed = ['all', 'not_started', 'attending', 'completed'];
        $this->attendanceFilter = in_array($attendanceFilter, $allowed, true) ? $attendanceFilter : 'all';
        $this->selectedActivity = $selectedActivity ?: null;
    }

    /**
     * Full report payload for the blade view.
     */
    public function build(): array
    {
        $context = $this->context();
        $rows = $this->buildRows($context);

        return [
            'attendance_filter'      => $this->attendanceFilter,
            'selected_activity'      => $this->selectedActivity,
            'activities'             => $this->activities(),
            'summary'                => $this->summaryFromRows($rows),
            'courses'                => $this->courseOverview($context, $rows),
            'most_viewed_courses'    => $this->rankCourses($context, $rows, 'starts'),
            'most_completed_courses' => $this->rankCourses($context, $rows, 'completed'),
            'department_rankings'    => $this->departmentRankings($rows),
            'rows'                   => $rows,
        ];
    }

    /**
     * Activity dropdown: sessions attached to at least one term.
     */
    public function activities(): array
    {
        $rows = DB::table('sessions as s')
            ->join('session_term as st', 'st.session_id', '=', 's.id')
            ->join('terms as t', 't.id', '=', 'st.term_id')
            ->orderBy('s.title')
            ->get(['s.id', 's.title', 't.title as term_title']);

        // One dropdown entry per session (activity), labelled "Activity — Term".
        return $rows->unique('id')
            ->map(fn ($a) => [
                'id'    => (int) $a->id,
                'label' => trim($a->title.' — '.$a->term_title, ' —'),
            ])
            ->sortBy('label')
            ->values()
            ->all();
    }

    /**
     * Shared lookup context used by all sections of the report.
     */
    protected function context(): array
    {
        // Every sessionable that belongs to a term, with its session/activity title.
        $activityQuery = DB::table('sessionables as sb')
            ->join('session_term as st', 'st.session_id', '=', 'sb.session_id')
            ->join('terms as t', 't.id', '=', 'st.term_id')
            ->join('sessions as s', 's.id', '=', 'sb.session_id')
            ->whereNull('sb.deleted_at')
            ->orderBy('st.term_id')
            ->orderBy('sb.order')
            ->get(['sb.id as sessionable_id', 'st.term_id', 'sb.session_id', 's.title as activity_title']);

        if ($this->selectedActivity !== null) {
            $activityQuery = $activityQuery->where('session_id', $this->selectedActivity);
        }

        $activitiesByTerm = $activityQuery->groupBy('term_id');

        // Workouts (learner activity records) joined to their participants.
        $workoutQuery = DB::table('workouts as w')
            ->join('term_user as tu', 'tu.id', '=', 'w.participant_id')
            ->join('users as u', 'u.id', '=', 'tu.user_id')
            ->where('w.is_mentor', false)
            ->get([
                'w.id as workout_id',
                'w.sessionable_id',
                'w.participant_id',
                'w.is_completed',
                'tu.user_id',
                'tu.term_id',
                'u.name as learner_name',
                'u.ad_department',
                DB::raw('CASE WHEN w.date_last_view IS NOT NULL THEN DATEDIFF(minute, w.date_first_view, w.date_last_view) ELSE 0 END as minutes'),
            ]);

        if ($this->selectedActivity !== null) {
            $workoutQuery = $workoutQuery->filter(fn ($w) => $this->sessionableIsSelected((int) $w->sessionable_id));
        }

        $workoutsByParticipant = $workoutQuery->groupBy('participant_id');

        // Quiz score per workout = average of its question-level scores.
        $quizByWorkout = [];
        $logs = DB::table('workout_quiz_logs')
            ->where('is_mentor', false)
            ->whereNotNull('score')
            ->get(['workout_id', 'score'])
            ->groupBy('workout_id');
        foreach ($logs as $wid => $group) {
            $quizByWorkout[(int) $wid] = round((float) $group->avg('score'), 2);
        }

        $enrollments = DB::table('term_user as tu')
            ->join('users as u', 'u.id', '=', 'tu.user_id')
            ->join('terms as t', 't.id', '=', 'tu.term_id')
            ->join('courses as c', 'c.id', '=', 't.course_id')
            ->where('tu.role_id', $this->learnerRoleId)
            ->orderBy('c.title')
            ->orderBy('u.name')
            ->get(['tu.id as participant_id', 'tu.term_id', 'u.id as user_id', 'u.name', 'u.[ldap_ou]', 't.title as term_title', 'c.id as course_id', 'c.title as course_title',
                'c.department_id as course_department_id']);

        if ($this->selectedActivity !== null) {
            $termsWithActivity = $activityQuery->pluck('term_id')->unique()->all();
            $enrollments = $enrollments->whereIn('term_id', $termsWithActivity);
        }

        $departments = DB::table('departments')->pluck('name', 'id');

        return compact(
            'activitiesByTerm',
            'workoutsByParticipant',
            'quizByWorkout',
            'enrollments',
            'departments'
        );
    }

    /**
     * Cache of sessionable_id -> session_id for the activity filter.
     */
    protected array $sessionableSessionMap = [];

    protected function sessionableIsSelected(int $sessionableId): bool
    {
        if (!$this->sessionableSessionMap) {
            $this->sessionableSessionMap = DB::table('sessionables')
                ->join('session_term', 'session_term.session_id', '=', 'sessionables.session_id')
                ->pluck('sessionables.session_id', 'sessionables.id')
                ->toArray();
        }

        return ($this->sessionableSessionMap[$sessionableId] ?? null) === $this->selectedActivity;
    }

    /**
     * Per-learner, per-course-term detail rows (filtered by attendance).
     */
    protected function buildRows(array $context): array
    {
        $rows = [];

        foreach ($context['enrollments'] as $e) {
            $activities = $context['activitiesByTerm'][$e->term_id] ?? collect();
            $totalActivities = $activities->count();

            $participantWorkouts = $context['workoutsByParticipant'][$e->participant_id] ?? collect();

            // Only count workouts on the filtered activity set when a filter is active.
            if ($this->selectedActivity !== null) {
                $relevantSessionables = $activities->pluck('sessionable_id')->all();
                $participantWorkouts = $participantWorkouts->whereIn('sessionable_id', $relevantSessionables);
            }

            $starts = $participantWorkouts->count();
            $done = $participantWorkouts->filter(fn ($w) => (bool) $w->is_completed)->count();

            $progress = $totalActivities > 0 ? (int) round(min($done, $totalActivities) / $totalActivities * 100) : 0;

            if ($starts === 0) {
                $status = 'not_started';
            } elseif ($totalActivities > 0 && $done >= $totalActivities) {
                $status = 'completed';
            } else {
                $status = 'attending';
            }

            if ($this->attendanceFilter !== 'all' && $this->attendanceFilter !== $status) {
                continue;
            }

            // Quiz score: average across this enrollment's workout quiz logs.
            $workoutIds = $participantWorkouts->pluck('workout_id')->all();
            $scores = collect($workoutIds)->map(fn ($id) => $context['quizByWorkout'][$id] ?? null)->filter();
            $quizScore = $scores->isNotEmpty() ? round((float) $scores->avg(), 1) : null;

            // Learning score: completion progress blended with quiz performance.
            $learningScore = $quizScore !== null
                ? (int) round($progress * 0.5 + $quizScore * 0.5)
                : $progress;

            $minutes = (int) round($participantWorkouts->sum('minutes'));

            $rows[] = [
                'learner_name'         => $e->name,
                'user_id'              => (int) $e->user_id,
                'ldap_ou'              => $e->ad_department ?: __('Unassigned'),
                'course_title'         => $e->course_title,
                'course_id'            => (int) $e->course_id,
                'term_title'           => $e->term_title,
                'term_id'              => (int) $e->term_id,
                'course_department'    => $context['departments'][$e->course_department_id] ?? null,
                'attendance_status'    => $status,
                'activity_status'      => $status,
                'progress'             => $progress,
                'completed_activities' => min($done, $totalActivities),
                'total_activities'     => $totalActivities,
                'activity_starts'      => $starts,
                'minutes_watched'      => $minutes,
                'learning_score'       => $learningScore,
                'quiz_score'           => $quizScore,
            ];
        }

        return $rows;
    }

    /**
     * Summary cards computed from the filtered rows.
     */
    protected function summaryFromRows(array $rows): array
    {
        $collection = collect($rows);

        return [
            'learners'              => $collection->pluck('user_id')->unique()->count(),
            'attending_learners'    => $collection->where('attendance_status', 'attending')->pluck('user_id')->unique()->count(),
            'average_progress'      => count($rows) ? (int) round($collection->avg('progress')) : 0,
            'completed_enrollments' => $collection->where('attendance_status', 'completed')->count(),
        ];
    }

    /**
     * Course overview table — every course, including those with no activity.
     */
    protected function courseOverview(array $context, array $rows): array
    {
        $courses = DB::table('courses')
            ->leftJoin('departments', 'departments.id', '=', 'courses.department_id')
            ->orderBy('courses.title')
            ->get(['courses.id', 'courses.title', 'departments.name as department']);

        $rowsByCourse = collect($rows)->groupBy('course_id');

        $out = [];

        foreach ($courses as $course) {
            $courseRows = $rowsByCourse->get($course->id, collect());

            $out[] = [
                'course_title'          => $course->title,
                'department'            => $course->department,
                'learner_count'         => $courseRows->pluck('user_id')->unique()->count(),
                'active_learner_count'  => $courseRows->where('activity_starts', '>', 0)->pluck('user_id')->unique()->count(),
                'average_progress'      => $courseRows->isNotEmpty() ? (int) round($courseRows->avg('progress')) : 0,
                'activity_starts'       => (int) $courseRows->sum('activity_starts'),
                'completed_enrollments' => $courseRows->where('attendance_status', 'completed')->count(),
            ];
        }

        return $out;
    }

    /**
     * Most viewed / most completed rankings built from the same overview data.
     */
    protected function rankCourses(array $context, array $rows, string $metric, int $limit = 8): array
    {
        $overview = $this->courseOverview($context, $rows);

        $ranked = collect($overview)
            ->map(function ($c) {
                $c['completion_rate'] = $c['learner_count'] > 0
                    ? (int) round($c['completed_enrollments'] / $c['learner_count'] * 100)
                    : 0;

                return $c;
            });

        if ($metric === 'starts') {
            $ranked = $ranked
                ->sortByDesc('activity_starts')
                ->filter(fn ($c) => $c['activity_starts'] > 0 || $c['active_learner_count'] > 0);
        } else {
            $ranked = $ranked
                ->sortByDesc('completed_enrollments')
                ->filter(fn ($c) => $c['completed_enrollments'] > 0);
        }

        return $ranked->take($limit)->values()->all();
    }

    /**
     * Department (LDAP OU) rankings: learners, learning & quiz averages, best learners.
     */
    protected function departmentRankings(array $rows): array
    {
        return collect($rows)
            ->groupBy('ldap_ou')
            ->map(function ($deptRows, $name) {
                // Collapse multiple course enrollments into per-learner averages.
                $perLearner = $deptRows->groupBy('user_id')->map(fn ($r) => [
                    'name'  => $r->first()['learner_name'],
                    'learn' => $r->avg('learning_score'),
                    'quiz'  => $r->filter(fn ($x) => $x['quiz_score'] !== null)->avg('quiz_score'),
                ]);

                $topLearn = $perLearner->sortByDesc('learn')->first();
                $quizLearners = $perLearner->filter(fn ($l) => $l['quiz'] !== null);
                $topQuiz = $quizLearners->sortByDesc('quiz')->first();

                return [
                    'name'                 => $name,
                    'learner_count'        => $perLearner->count(),
                    'learning_score'       => $perLearner->avg('learn') !== null ? round((float) $perLearner->avg('learn'), 1) : null,
                    'quiz_score'           => $quizLearners->isNotEmpty() ? round((float) $quizLearners->avg('quiz'), 1) : null,
                    'top_learning_learner' => $topLearn ? ['name' => $topLearn['name'], 'score' => round((float) $topLearn['learn'], 1)] : null,
                    'top_quiz_learner'     => $topQuiz ? ['name' => $topQuiz['name'], 'score' => round((float) $topQuiz['quiz'], 1)] : null,
                ];
            })
            ->sortByDesc(fn ($d) => ($d['learning_score'] ?? 0) + ($d['quiz_score'] ?? 0))
            ->values()
            ->all();
    }
}
