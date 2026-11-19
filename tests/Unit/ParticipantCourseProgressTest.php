<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\Participant;
use App\Models\Term;
use App\Utility\Modules\Terms\ParticipantInfoGenerator;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class ParticipantCourseProgressTest extends TestCase
{
    public function test_it_aggregates_progress_across_all_terms_in_a_course(): void
    {
        $course = new Course(['title' => 'Leadership']);
        $course->id = 10;

        $firstTerm = new Term();
        $firstTerm->total_task = 10;
        $firstTerm->task_done = 3;
        $firstTerm->setRelation('Course', $course);

        $secondTerm = new Term();
        $secondTerm->total_task = 10;
        $secondTerm->task_done = 5;
        $secondTerm->setRelation('Course', $course);

        $progress = ParticipantInfoGenerator::calculateCourseProgress(new Collection([
            $this->participantWithTerm($firstTerm),
            $this->participantWithTerm($secondTerm),
        ]));

        $this->assertSame([
            [
                'id' => 10,
                'title' => 'Leadership',
                'completed_tasks' => 8,
                'total_tasks' => 20,
                'percentage' => 40,
            ],
        ], $progress);
    }

    public function test_it_returns_zero_for_a_course_without_tasks(): void
    {
        $course = new Course(['title' => 'Empty Course']);
        $course->id = 11;

        $term = new Term();
        $term->total_task = 0;
        $term->task_done = 0;
        $term->setRelation('Course', $course);

        $progress = ParticipantInfoGenerator::calculateCourseProgress(new Collection([
            $this->participantWithTerm($term),
        ]));

        $this->assertSame(0, $progress[0]['percentage']);
    }

    public function test_it_caps_course_progress_at_one_hundred_percent(): void
    {
        $course = new Course(['title' => 'Completed Course']);
        $course->id = 12;

        $term = new Term();
        $term->total_task = 5;
        $term->task_done = 7;
        $term->setRelation('Course', $course);

        $progress = ParticipantInfoGenerator::calculateCourseProgress(new Collection([
            $this->participantWithTerm($term),
        ]));

        $this->assertSame(100, $progress[0]['percentage']);
    }

    private function participantWithTerm(Term $term): Participant
    {
        $participant = new Participant();
        $participant->setRelation('Term', $term);

        return $participant;
    }
}
