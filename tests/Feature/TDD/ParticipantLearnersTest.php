<?php

namespace Tests\Feature\TDD;

use App\Models\Participant;
use App\Models\Role;
use App\Models\Term;
use App\Models\User;
use Tests\BaseTest;

class ParticipantLearnersTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_super_admin_role_does_not_hide_a_student_from_mentor_learners()
    {
        $participant = Participant::where('role_id', 4)->firstOrFail();
        $participant->User->assignRole('Super-Admin');

        $this->signIn();

        $learners = Participant::learners()->get();

        $this->assertTrue($learners->contains('id', $participant->id));
        $this->assertTrue($learners->every(function (Participant $learner) {
            return (int) $learner->role_id === 4;
        }));
    }

    public function test_user_with_student_and_super_admin_roles_is_listed_even_if_term_role_is_different()
    {
        $studentRole = Role::where('name', 'student')->firstOrFail();
        $superAdminRole = Role::where('name', 'Super-Admin')->firstOrFail();
        $student = User::factory()->create();
        $student->assignRole($studentRole);
        $student->assignRole($superAdminRole);
        $this->assertSame(4, $studentRole->id);
        $this->assertTrue($student->fresh()->hasRole('student'));

        $participant = Participant::create([
            'term_id' => Term::query()->value('id'),
            'user_id' => $student->id,
            'role_id' => $superAdminRole->id,
        ]);
        $this->signIn();

        $learners = Participant::learners()->get();

        $this->assertTrue($learners->contains('user_id', $student->id));
    }
}
