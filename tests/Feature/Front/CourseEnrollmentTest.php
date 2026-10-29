<?php

namespace Tests\Feature\Front;

use App\Models\Course;
use App\Models\File;
use App\Models\Role;
use App\Models\Session;
use App\Models\Term;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourseEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_for_course_files()
    {
        $this->get(route('front.course.file', ['path' => 'files/material.pdf']))
            ->assertRedirect(route('login'));
    }

    public function test_learner_can_join_all_published_terms_and_access_course_files()
    {
        Storage::fake('public');
        Storage::disk('public')->put('files/material.txt', 'course material');

        $this->createLearnerRole();
        $course = Course::factory()->create(['allow_self_enrollment' => true]);
        $publishedTerm = $this->createTerm($course, true);
        $unpublishedTerm = $this->createTerm($course, false);
        $file = $this->attachFileToTerm($publishedTerm, 'files/material.txt');
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('front.course.join', $course))
            ->assertRedirect(route('front.term.sessions', [
                'course' => $course->id,
                'term' => $publishedTerm->id,
            ]));

        $this->assertDatabaseHas('term_user', [
            'term_id' => $publishedTerm->id,
            'user_id' => $user->id,
            'role_id' => 4,
        ]);
        $this->assertDatabaseMissing('term_user', [
            'term_id' => $unpublishedTerm->id,
            'user_id' => $user->id,
        ]);

        $this->get(route('front.course.file', ['path' => $file->file]))
            ->assertOk();
    }

    public function test_enrolled_learner_can_access_assigned_course_files()
    {
        Storage::fake('public');
        Storage::disk('public')->put('files/assigned.txt', 'assigned material');

        $this->createLearnerRole();
        $course = Course::factory()->create(['allow_self_enrollment' => false]);
        $term = $this->createTerm($course, true);
        $file = $this->attachFileToTerm($term, 'files/assigned.txt');
        $user = User::factory()->create();
        $term->Participants()->attach($user->id, ['role_id' => 4]);

        $this->actingAs($user)
            ->get(route('front.course.file', ['path' => $file->file]))
            ->assertOk();
    }

    public function test_public_course_files_require_enrollment()
    {
        Storage::fake('public');
        Storage::disk('public')->put('files/not-joined.txt', 'course material');

        $this->createLearnerRole();
        $course = Course::factory()->create(['allow_self_enrollment' => true]);
        $term = $this->createTerm($course, true);
        $file = $this->attachFileToTerm($term, 'files/not-joined.txt');
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('front.course.file', ['path' => $file->file]))
            ->assertRedirect(route('front.course', $course->id));
    }

    public function test_unassigned_learner_cannot_access_assigned_course_files()
    {
        Storage::fake('public');
        Storage::disk('public')->put('files/private.txt', 'assigned material');

        $this->createLearnerRole();
        $course = Course::factory()->create(['allow_self_enrollment' => false]);
        $term = $this->createTerm($course, true);
        $file = $this->attachFileToTerm($term, 'files/private.txt');
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('front.course.file', ['path' => $file->file]))
            ->assertForbidden();
    }

    private function createLearnerRole(): void
    {
        foreach (['admin', 'supervisor', 'mentor', 'student'] as $index => $name) {
            Role::create([
                'id' => $index + 1,
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }
    }

    private function createTerm(Course $course, bool $published): Term
    {
        return Term::factory()->create([
            'course_id' => $course->id,
            'department_id' => $course->department_id,
            'is_published' => $published,
        ]);
    }

    private function attachFileToTerm(Term $term, string $path): File
    {
        $session = Session::factory()->create();
        $term->Sessions()->attach($session->id);
        $file = File::factory()->create(['file' => $path]);
        $session->Files()->attach($file->id);

        return $file;
    }
}
