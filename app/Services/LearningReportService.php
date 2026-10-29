<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Sessionable;
use App\Models\Workout;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LearningReportService
{
    public function build(string $attendanceFilter = 'all', ?int $activityFilter = null): array
    {
        $studentRoleId = Role::where('name', 'student')->value('id');
        $enrollments = collect();

        if ($studentRoleId) {
            $enrollments = DB::table('term_user as participant')
                ->join('users', 'users.id', '=', 'participant.user_id')
                ->join('terms', 'terms.id', '=', 'participant.term_id')
                ->join('courses', 'courses.id', '=', 'terms.course_id')
                ->leftJoin('departments', 'departments.id', '=', 'courses.department_id')
                ->where('participant.role_id', $studentRoleId)
                ->select([
                    'participant.id as participant_id',
                    'participant.term_id',
                    'participant.user_id',
                    'users.name as learner_name',
                    'users.ldap_ou',
                    'users.is_ldap_user',
                    'terms.title as term_title',
                    'courses.id as course_id',
                    'courses.title as course_title',
                    'departments.title as course_department',
                ])
                ->orderBy('courses.title')
                ->orderBy('users.name')
                ->get();
        }

        $activityLinks = DB::table('session_term')
            ->join('sessionables', 'sessionables.session_id', '=', 'session_term.session_id')
            ->whereNull('sessionables.deleted_at')
            ->select([
                'session_term.term_id',
                'sessionables.id as sessionable_id',
                'sessionables.sessionable_type',
            ])
            ->get();

        $activityIds = $activityLinks->pluck('sessionable_id')->unique()->values();
        $sessionables = $activityIds->isEmpty()
            ? collect()
            : Sessionable::whereIn('id', $activityIds)->get()->keyBy('id');
        $activityTitles = [];

        foreach ($sessionables->groupBy('sessionable_type') as $type => $typedSessionables) {
            if (!class_exists($type)) {
                continue;
            }

            $model = new $type();
            $relatedActivities = DB::table($model->getTable())
                ->whereIn('id', $typedSessionables->pluck('sessionable_id'))
                ->get(['id', 'title'])
                ->keyBy('id');

            foreach ($typedSessionables as $sessionable) {
                $relatedActivity = $relatedActivities->get($sessionable->sessionable_id);
                if ($relatedActivity) {
                    $activityTitles[$sessionable->id] = $relatedActivity->title;
                }
            }
        }

        $activityOptions = $activityIds->map(function ($id) use ($sessionables, $activityTitles) {
            $sessionable = $sessionables->get($id);
            $type = $sessionable ? class_basename($sessionable->sessionable_type) : 'Activity';
            $title = $activityTitles[$id] ?? $type;

            return [
                'id' => (int) $id,
                'label' => $title . ' (' . $type . ')',
            ];
        })->sortBy('label')->values();

        $activitiesByTerm = $activityLinks
            ->groupBy('term_id')
            ->map(function (Collection $links) {
                return $links->pluck('sessionable_id')->unique()->values()->all();
            });

        $participantIds = $enrollments->pluck('participant_id')->unique()->values();
        $workoutsByParticipant = $participantIds->isEmpty()
            ? collect()
            : Workout::whereIn('participant_id', $participantIds)
                ->get(['participant_id', 'sessionable_id', 'is_completed', 'score'])
                ->groupBy('participant_id');

        $courses = DB::table('courses')
            ->leftJoin('departments', 'departments.id', '=', 'courses.department_id')
            ->select([
                'courses.id',
                'courses.title',
                'departments.title as department_title',
            ])
            ->orderBy('courses.title')
            ->get();

        $courseStats = [];
        foreach ($courses as $course) {
            $courseStats[$course->id] = [
                'course_id' => (int) $course->id,
                'course_title' => $course->title,
                'department' => $course->department_title,
                'enrollments' => 0,
                'learners' => [],
                'active_learners' => [],
                'activity_starts' => 0,
                'completed_enrollments' => 0,
                'progress_total' => 0,
            ];
        }

        $rows = [];
        foreach ($enrollments as $enrollment) {
            $activityIdsForTerm = $activitiesByTerm->get($enrollment->term_id, []);
            if ($activityFilter !== null && !in_array($activityFilter, $activityIdsForTerm)) {
                continue;
            }

            $participantWorkouts = $workoutsByParticipant->get($enrollment->participant_id, collect())
                ->keyBy('sessionable_id');
            $startedCount = 0;
            $completedCount = 0;
            $learningScoreSum = 0;
            $learningScoreCount = 0;
            $quizScoreSum = 0;
            $quizScoreCount = 0;

            foreach ($activityIdsForTerm as $sessionableId) {
                $workout = $participantWorkouts->get($sessionableId);
                if (!$workout) {
                    continue;
                }

                $startedCount++;
                if (!$workout->is_completed) {
                    continue;
                }

                $completedCount++;
                $sessionable = $sessionables->get($sessionableId);
                $isQuiz = $sessionable
                    && strtolower(class_basename($sessionable->sessionable_type)) === 'quiz';

                if ($isQuiz) {
                    $quizScoreSum += (int) $workout->score;
                    $quizScoreCount++;
                } else {
                    $learningScoreSum += (int) $workout->score;
                    $learningScoreCount++;
                }
            }

            $totalActivities = count($activityIdsForTerm);
            $attendanceStatus = $totalActivities > 0 && $completedCount === $totalActivities
                ? 'completed'
                : ($startedCount > 0 ? 'attending' : 'not_started');

            if ($attendanceFilter !== 'all' && $attendanceStatus !== $attendanceFilter) {
                continue;
            }

            $selectedActivityStatus = null;
            if ($activityFilter !== null) {
                $activityWorkout = $participantWorkouts->get($activityFilter);
                $selectedActivityStatus = !$activityWorkout
                    ? 'not_started'
                    : ($activityWorkout->is_completed ? 'completed' : 'attending');
            }

            $progress = $totalActivities > 0
                ? round(($completedCount / $totalActivities) * 100, 1)
                : 0;
            $ldapOu = $enrollment->ldap_ou ?: (
                $enrollment->is_ldap_user ? 'Awaiting AD sign-in' : 'Not an AD account'
            );

            $row = [
                'participant_id' => (int) $enrollment->participant_id,
                'user_id' => (int) $enrollment->user_id,
                'learner_name' => $enrollment->learner_name,
                'ldap_ou' => $ldapOu,
                'course_id' => (int) $enrollment->course_id,
                'course_title' => $enrollment->course_title,
                'course_department' => $enrollment->course_department,
                'term_title' => $enrollment->term_title,
                'attendance_status' => $attendanceStatus,
                'activity_status' => $selectedActivityStatus,
                'completed_activities' => $completedCount,
                'total_activities' => $totalActivities,
                'activity_starts' => $startedCount,
                'progress' => $progress,
                'learning_score' => $learningScoreCount > 0
                    ? round($learningScoreSum / $learningScoreCount, 1)
                    : null,
                'quiz_score' => $quizScoreCount > 0
                    ? round($quizScoreSum / $quizScoreCount, 1)
                    : null,
                'learning_score_sum' => $learningScoreSum,
                'learning_score_count' => $learningScoreCount,
                'quiz_score_sum' => $quizScoreSum,
                'quiz_score_count' => $quizScoreCount,
            ];
            $rows[] = $row;

            $courseStats[$enrollment->course_id]['enrollments']++;
            $courseStats[$enrollment->course_id]['learners'][$enrollment->user_id] = true;
            $courseStats[$enrollment->course_id]['activity_starts'] += $startedCount;
            $courseStats[$enrollment->course_id]['progress_total'] += $progress;
            if ($startedCount > 0) {
                $courseStats[$enrollment->course_id]['active_learners'][$enrollment->user_id] = true;
            }
            if ($attendanceStatus === 'completed') {
                $courseStats[$enrollment->course_id]['completed_enrollments']++;
            }
        }

        $courseStats = array_values(array_map(function (array $course) {
            $course['learner_count'] = count($course['learners']);
            $course['active_learner_count'] = count($course['active_learners']);
            $course['average_progress'] = $course['enrollments'] > 0
                ? round($course['progress_total'] / $course['enrollments'], 1)
                : 0;
            $course['completion_rate'] = $course['enrollments'] > 0
                ? round(($course['completed_enrollments'] / $course['enrollments']) * 100, 1)
                : 0;
            unset($course['learners'], $course['active_learners'], $course['progress_total']);

            return $course;
        }, $courseStats));

        $mostViewed = array_values(array_filter($courseStats, function (array $course) {
            return $course['active_learner_count'] > 0;
        }));
        usort($mostViewed, function (array $left, array $right) {
            return ($right['active_learner_count'] <=> $left['active_learner_count'])
                ?: ($right['activity_starts'] <=> $left['activity_starts']);
        });

        $mostCompleted = array_values(array_filter($courseStats, function (array $course) {
            return $course['completed_enrollments'] > 0;
        }));
        usort($mostCompleted, function (array $left, array $right) {
            return ($right['completed_enrollments'] <=> $left['completed_enrollments'])
                ?: ($right['completion_rate'] <=> $left['completion_rate']);
        });

        /** @var array<string, array{learners: array<int, array{name: string, learning_sum: int, learning_count: int, quiz_sum: int, quiz_count: int}>}> $departments */
        $departments = [];
        foreach ($rows as $row) {
            $departmentName = $row['ldap_ou'];
            if (!isset($departments[$departmentName])) {
                $departments[$departmentName] = ['learners' => []];
            }

            if (!isset($departments[$departmentName]['learners'][$row['user_id']])) {
                $departments[$departmentName]['learners'][$row['user_id']] = [
                    'name' => $row['learner_name'],
                    'learning_sum' => 0,
                    'learning_count' => 0,
                    'quiz_sum' => 0,
                    'quiz_count' => 0,
                ];
            }

            $learner = &$departments[$departmentName]['learners'][$row['user_id']];
            $learner['learning_sum'] += $row['learning_score_sum'];
            $learner['learning_count'] += $row['learning_score_count'];
            $learner['quiz_sum'] += $row['quiz_score_sum'];
            $learner['quiz_count'] += $row['quiz_score_count'];
            unset($learner);
        }

        /** @var list<array{name: string, learner_count: int, learning_score: float|null, top_learning_learner: array{name: string, score: float}|null, quiz_score: float|null, top_quiz_learner: array{name: string, score: float}|null}> $departmentStats */
        $departmentStats = [];
        foreach ($departments as $departmentName => $department) {
            /** @var list<float> $learningScores */
            $learningScores = [];
            /** @var list<float> $quizScores */
            $quizScores = [];
            /** @var array{name: string, score: float}|null $topLearningLearner */
            $topLearningLearner = null;
            /** @var array{name: string, score: float}|null $topQuizLearner */
            $topQuizLearner = null;

            foreach ($department['learners'] as $learner) {
                $learnerLearningScore = $learner['learning_count'] > 0
                    ? round($learner['learning_sum'] / $learner['learning_count'], 1)
                    : null;
                $learnerQuizScore = $learner['quiz_count'] > 0
                    ? round($learner['quiz_sum'] / $learner['quiz_count'], 1)
                    : null;

                if ($learnerLearningScore !== null) {
                    $learningScores[] = $learnerLearningScore;
                    if ($topLearningLearner === null || $learnerLearningScore > $topLearningLearner['score']) {
                        $topLearningLearner = ['name' => $learner['name'], 'score' => $learnerLearningScore];
                    }
                }

                if ($learnerQuizScore !== null) {
                    $quizScores[] = $learnerQuizScore;
                    if ($topQuizLearner === null || $learnerQuizScore > $topQuizLearner['score']) {
                        $topQuizLearner = ['name' => $learner['name'], 'score' => $learnerQuizScore];
                    }
                }
            }

            $departmentStats[] = [
                'name' => $departmentName,
                'learner_count' => count($department['learners']),
                'learning_score' => $learningScores
                    ? round(array_sum($learningScores) / count($learningScores), 1)
                    : null,
                'top_learning_learner' => $topLearningLearner,
                'quiz_score' => $quizScores
                    ? round(array_sum($quizScores) / count($quizScores), 1)
                    : null,
                'top_quiz_learner' => $topQuizLearner,
            ];
        }

        usort($departmentStats, function (array $left, array $right) {
            return ($right['learning_score'] <=> $left['learning_score'])
                ?: ($right['quiz_score'] <=> $left['quiz_score']);
        });
        /** @var list<array{name: string, learner_count: int, learning_score: float|null, top_learning_learner: array{name: string, score: float}|null, quiz_score: float|null, top_quiz_learner: array{name: string, score: float}|null}> $learningDepartments */
        $learningDepartments = array_values(array_filter($departmentStats, function (array $department) {
            return $department['learning_score'] !== null;
        }));
        /** @var list<array{name: string, learner_count: int, learning_score: float|null, top_learning_learner: array{name: string, score: float}|null, quiz_score: float|null, top_quiz_learner: array{name: string, score: float}|null}> $quizDepartments */
        $quizDepartments = array_values(array_filter($departmentStats, function (array $department) {
            return $department['quiz_score'] !== null;
        }));

        $learnerIds = [];
        $attendingLearnerIds = [];
        $progressTotal = 0;
        $activityStarts = 0;
        $completedEnrollments = 0;
        foreach ($rows as $row) {
            $learnerIds[$row['user_id']] = true;
            if ($row['attendance_status'] === 'attending') {
                $attendingLearnerIds[$row['user_id']] = true;
            }
            $progressTotal += $row['progress'];
            $activityStarts += $row['activity_starts'];
            if ($row['attendance_status'] === 'completed') {
                $completedEnrollments++;
            }
        }

        return [
            'rows' => $rows,
            'activities' => $activityOptions->all(),
            'selected_activity' => $activityFilter,
            'attendance_filter' => $attendanceFilter,
            'summary' => [
                'learners' => count($learnerIds),
                'attending_learners' => count($attendingLearnerIds),
                'enrollments' => count($rows),
                'completed_enrollments' => $completedEnrollments,
                'average_progress' => count($rows) > 0
                    ? round($progressTotal / count($rows), 1)
                    : 0,
                'activity_starts' => $activityStarts,
            ],
            'courses' => $courseStats,
            'most_viewed_courses' => array_slice($mostViewed, 0, 5),
            'most_completed_courses' => array_slice($mostCompleted, 0, 5),
            'department_rankings' => $departmentStats,
            'top_learning_department' => $learningDepartments[0] ?? null,
            'top_quiz_department' => $quizDepartments[0] ?? null,
        ];
    }
}
