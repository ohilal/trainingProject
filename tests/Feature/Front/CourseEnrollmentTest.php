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
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CourseEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_for_course_files()
    {
        $this->get(route('front.course.file', ['path' => 'files/material.pdf']))
            ->assertRedirect(route('login'));
    }

    public function test_learner_can_join_all_course_terms_and_access_course_files()
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
        $this->assertDatabaseHas('term_user', [
            'term_id' => $unpublishedTerm->id,
            'user_id' => $user->id,
            'role_id' => 4,
        ]);

        $this->get(route('front.course.file', ['path' => $file->file]))
            ->assertOk();
    }

    public function test_learner_is_assigned_to_unpublished_terms_when_no_terms_are_published()
    {
        $this->createLearnerRole();
        $course = Course::factory()->create(['allow_self_enrollment' => true]);
        $term = $this->createTerm($course, false);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('front.course.join', $course))
            ->assertRedirect(route('front.course', $course->id))
            ->assertSessionHas('warning');

        $this->assertDatabaseHas('term_user', [
            'term_id' => $term->id,
            'user_id' => $user->id,
            'role_id' => 4,
        ]);
    }

    public function test_self_enrolled_terms_are_shown_in_my_courses()
    {
        $this->createLearnerRole();
        Permission::create([
            'name' => 'myCourse.index',
            'guard_name' => 'web',
        ]);
        Role::findOrFail(4)->givePermissionTo('myCourse.index');

        $course = Course::factory()->create(['allow_self_enrollment' => true]);
        $term = $this->createTerm($course, true);
        $user = User::factory()->create();
        $user->assignRole('student');

        $this->actingAs($user)
            ->post(route('front.course.join', $course));

        $this->get(route('myCourse'))
            ->assertOk()
            ->assertSeeText($term->title);
    }

    public function test_course_join_button_is_shown_to_unassigned_learner()
    {
        $this->createLearnerRole();
        $course = Course::factory()->create(['allow_self_enrollment' => true]);
        $this->createTerm($course, true);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('front.course', $course))
            ->assertOk()
            ->assertSee('اشترك فى هذه الدورة');
    }

    public function test_course_join_button_is_hidden_for_assigned_participant()
    {
        $this->createLearnerRole();
        $course = Course::factory()->create(['allow_self_enrollment' => true]);
        $term = $this->createTerm($course, true);
        $user = User::factory()->create();
        $term->Participants()->attach($user->id, ['role_id' => 3]);
        $this->assertTrue(
            $course->Terms()
                ->whereHas('Participants', function ($query) use ($user) {
                    $query->where('users.id', $user->id);
                })
                ->exists()
        );

        $response = $this->actingAs($user)
            ->get(route('front.course', $course));

        $response->assertViewHas('isAssignedToCourse', true)
            ->assertOk()
            ->assertDontSee('اشترك فى هذه الدورة');
    }

    public function test_course_join_button_is_hidden_for_enrolled_learner()
    {
        $this->createLearnerRole();
        $course = Course::factory()->create(['allow_self_enrollment' => true]);
        $term = $this->createTerm($course, true);
        $user = User::factory()->create();
        $term->Participants()->attach($user->id, ['role_id' => 4]);

        $this->actingAs($user)
            ->get(route('front.course', $course))
            ->assertOk()
            ->assertDontSee('اشترك فى هذه الدورة');
    }

    public function test_partially_enrolled_learner_can_complete_self_enrollment()
    {
        $this->createLearnerRole();
        $course = Course::factory()->create(['allow_self_enrollment' => true]);
        $assignedTerm = $this->createTerm($course, true);
        $unassignedTerm = $this->createTerm($course, false);
        $user = User::factory()->create();
        $assignedTerm->Participants()->attach($user->id, ['role_id' => 4]);

        $this->actingAs($user)
            ->get(route('front.course', $course))
            ->assertOk()
            ->assertSee('اشترك فى هذه الدورة');

        $this->post(route('front.course.join', $course))
            ->assertRedirect(route('front.term.sessions', [
                'course' => $course->id,
                'term' => $assignedTerm->id,
            ]));

        $this->assertDatabaseHas('term_user', [
            'term_id' => $unassignedTerm->id,
            'user_id' => $user->id,
            'role_id' => 4,
        ]);
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
