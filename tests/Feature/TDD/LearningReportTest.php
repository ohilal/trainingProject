<?php

namespace Tests\Feature\TDD;

use App\Models\File;
use App\Models\Participant;
use App\Models\Sessionable;
use App\Models\User;
use App\Models\Workout;
use App\Services\LearningReportService;
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
            ->assertSee('Department score rankings');
    }

    public function test_student_cannot_view_learning_report()
    {
        $this->signIn(8);

        $this->get(route('reports.learning'))->assertForbidden();
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
}
