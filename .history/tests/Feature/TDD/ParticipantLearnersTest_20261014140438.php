<?php

namespace Tests\Feature\TDD;

use App\Models\Participant;
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
}
