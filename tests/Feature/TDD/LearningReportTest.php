<?php

namespace Tests\Feature\TDD;

use App\Models\File;
use App\Models\Participant;
use App\Models\Sessionable;
use App\Models\User;
use App\Models\Workout;
use App\Http\Controllers\LearningReportController;
use App\Services\LearningReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\BaseTest;

class LearningReportTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_authorized_admin_can_view_learning_report()
    {
        $this->signIn();

        $this->get(route('reports.learning'))
            ->assertOk()
            ->assertSee('Learning report')
            ->assertSee('Course overview')
            ->assertSee('Most viewed courses')
            ->assertSee('Department score rankings')
            ->assertSee('LDAP OU');
    }

    public function test_student_cannot_view_learning_report()
    {
        $this->signIn(8);

        $this->get(route('reports.learning'))->assertForbidden();
    }

    public function test_report_ignores_cached_data_from_previous_completion_calculation()
    {
        Cache::put('reports.learning.all.all', ['stale' => true], 600);

        $view = app(LearningReportController::class)->index(Request::create('/reports/learning'));
        $report = $view->getData()['report'];

        $this->assertArrayHasKey('courses', $report);
        $this->assertArrayHasKey('summary', $report);
    }

    public function test_report_calculates_course_progress_and_separates_quiz_scores()
    {
        $learner = User::findOrFail(8);
        $learner->update([
            'is_ldap_user' => true,
            'ldap_ou' => 'Training / Adults',
        ]);

        $participant = Participant::where('term_id', 1)
            ->where('user_id', $learner->id)
            ->firstOrFail();
        $fileActivity = Sessionable::where('session_id', 1)
            ->where('sessionable_type', File::class)
            ->firstOrFail();
        $quizActivity = Sessionable::where('session_id', 1)
            ->where('sessionable_type', 'App\\Models\\Quiz')
            ->firstOrFail();

        Workout::create([
            'participant_id' => $participant->id,
            'sessionable_id' => $fileActivity->id,
            'date_first_view' => now(),
            'is_completed' => true,
            'score' => 90,
        ]);
        Workout::create([
            'participant_id' => $participant->id,
            'sessionable_id' => $quizActivity->id,
            'date_first_view' => now(),
            'is_completed' => true,
            'score' => 80,
        ]);

        $report = app(LearningReportService::class)->build();
        $row = collect($report['rows'])->first(function (array $row) use ($participant) {
            return $row['participant_id'] === $participant->id;
        });

        $this->assertNotNull($row);
        $this->assertSame('Training / Adults', $row['ldap_ou']);
        $this->assertSame('attending', $row['attendance_status']);
        $this->assertSame(2, $row['completed_activities']);
        $this->assertSame(17, $row['total_activities']);
        $this->assertSame(11.8, $row['progress']);
        $this->assertSame(90.0, $row['learning_score']);
        $this->assertSame(80.0, $row['quiz_score']);

        $course = collect($report['courses'])->firstWhere('course_id', 1);
        $this->assertSame(1, $course['active_learner_count']);
        $this->assertSame(2, $course['activity_starts']);

        $department = collect($report['department_rankings'])->firstWhere('name', 'Training / Adults');
        $this->assertSame(90.0, $department['learning_score']);
        $this->assertSame(80.0, $department['quiz_score']);
        $this->assertSame('student', $department['top_learning_learner']['name']);
        $this->assertSame('student', $department['top_quiz_learner']['name']);

        $filteredReport = app(LearningReportService::class)->build('attending', $fileActivity->id);
        $this->assertCount(1, $filteredReport['rows']);
        $this->assertSame('attending', $filteredReport['rows'][0]['attendance_status']);
        $this->assertSame('completed', $filteredReport['rows'][0]['activity_status']);
    }

    public function test_course_achievement_requires_completion_across_all_terms_and_counts_learners_once()
    {
        $learner = User::findOrFail(8);
        $participants = DB::table('term_user')
            ->where('user_id', $learner->id)
            ->whereIn('term_id', [1, 2])
            ->pluck('id', 'term_id');
        $activitiesByTerm = DB::table('sessionables as sb')
            ->join('session_term as st', 'st.session_id', '=', 'sb.session_id')
            ->whereIn('st.term_id', [1, 2])
            ->whereNull('sb.deleted_at')
            ->get(['st.term_id', 'sb.id as sessionable_id'])
            ->groupBy('term_id');

        foreach ($activitiesByTerm[1] as $activity) {
            Workout::create([
                'participant_id' => $participants[1],
                'sessionable_id' => $activity->sessionable_id,
                'date_first_view' => now(),
                'is_completed' => true,
            ]);
        }

        $withoutFullCourseCompletion = app(LearningReportService::class)->build();
        $this->assertNull(collect($withoutFullCourseCompletion['most_completed_courses'])->firstWhere('course_id', 1));

        foreach ($activitiesByTerm[2] as $activity) {
            Workout::create([
                'participant_id' => $participants[2],
                'sessionable_id' => $activity->sessionable_id,
                'date_first_view' => now(),
                'is_completed' => true,
            ]);
        }

        $otherLearner = User::factory()->create();
        $studentRoleId = DB::table('roles')->where('name', 'student')->value('id');
        $otherParticipants = collect([1, 2])->mapWithKeys(function ($termId) use ($otherLearner, $studentRoleId) {
            $participantId = DB::table('term_user')->insertGetId([
                'term_id' => $termId,
                'user_id' => $otherLearner->id,
                'role_id' => $studentRoleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return [$termId => $participantId];
        });

        foreach ($activitiesByTerm[1] as $activity) {
            Workout::create([
                'participant_id' => $otherParticipants[1],
                'sessionable_id' => $activity->sessionable_id,
                'date_first_view' => now(),
                'is_completed' => true,
            ]);
        }

        foreach ($activitiesByTerm[2]->take($activitiesByTerm[2]->count() - 1) as $activity) {
            Workout::create([
                'participant_id' => $otherParticipants[2],
                'sessionable_id' => $activity->sessionable_id,
                'date_first_view' => now(),
                'is_completed' => true,
            ]);
        }

        $completedCohortReport = app(LearningReportService::class)->build('completed');
        $course = collect($completedCohortReport['most_completed_courses'])->firstWhere('course_id', 1);

        $this->assertNotNull($course);
        $this->assertSame(2, $course['learner_count']);
        $this->assertSame(1, $course['completed_enrollments']);
        $this->assertSame(50, $course['completion_rate']);
    }
}
