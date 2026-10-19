<?php

namespace Tests\Feature\TDD\Student\Workout;

use App\Models\Workout;
use App\Models\Participant;
use App\Models\Sessionable;
use Tests\BaseTest;

class workoutTest extends BaseTest
{

    private $workout = 'App\Models\Workout';
    private $workoutQuiz = 'App\Models\WorkoutQuizLog';


    public function student(){
        $this->signIn(8);
    }

    protected function setUp(): void
    {

        parent::setUp();
        $this->seed();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_first_term_is_exist()
    {
        $this->student();
        $response = $this->get(route('learningCourse', ['participant' => 3]));

        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_first_view_set_record()
    {
        $this->student();
        $response = $this->get(route('taskLearner', [
            'participant' => 3,
            'sessionable' => 1
        ]));
        

        $response->assertStatus(200);

        $this->assertDatabaseHas(
            $this->workout,
            [
                'participant_id' => "3",
                'sessionable_id' => "1",
                'is_completed' => "0"
            ]
        );
    }



    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_first_view_quiz_set_record()
    {
        $this->student();
        $response = $this->get(route('taskLearner', [
            'participant' => 3,
            'sessionable' => 16
        ]));
        

        $response->assertSee('Start');

    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_prepare_quiz_set_record()
    {
        $this->student();
        $response = $this->post(route('taskLearner', [
            'participant' => 3,
            'sessionable' => 16
        ]));
        

        $response->assertRedirect(route('taskLearner', ['participant' => 3, 'sessionable' => 16]));

    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_completed_and_next()
    {
        $this->student();
        $participant = Participant::findOrFail(3);
        $sessionable = Sessionable::findOrFail(1);
        $lastSessionable = Sessionable::where('session_id', $sessionable->session_id)
            ->orderBy('order')
            ->orderBy('id')
            ->get()
            ->last();

        Workout::create([
            'participant_id' => $participant->id,
            'sessionable_id' => $lastSessionable->id,
            'date_first_view' => now(),
        ]);

        $sessions = $participant->Term->Sessions;
        $currentSessionIndex = $sessions->search(function ($session) use ($sessionable) {
            return (int) $session->id === (int) $sessionable->session_id;
        });
        $nextSessionable = null;

        foreach ($sessions->slice($currentSessionIndex + 1) as $session) {
            $nextSessionable = $session->Related->first();

            if ($nextSessionable) {
                break;
            }
        }

        $this->assertNotNull($nextSessionable);

        $workout = Workout::where('participant_id', $participant->id)
            ->where('sessionable_id', $lastSessionable->id)
            ->firstOrFail();
        $response = $this->get(route('completedAndNext', ['workout' => $workout->id]));

        $response->assertRedirect(route('taskLearner', [
            'participant' => $participant->id,
            'sessionable' => $nextSessionable->id
        ]));

        $this->assertDatabaseHas($this->workout, [
            'id' => $workout->id,
            'is_completed' => 1,
            'score' => 100
        ]);
    }

    public function test_session_progress_uses_activities_from_that_session()
    {
        $this->student();
        $this->get(route('taskLearner', [
            'participant' => 3,
            'sessionable' => 1
        ]));

        $sessionable = Sessionable::findOrFail(1);
        $sessionables = Sessionable::where('session_id', $sessionable->session_id)->get();

        foreach ($sessionables as $relatedSessionable) {
            Workout::updateOrCreate(
                [
                    'participant_id' => 3,
                    'sessionable_id' => $relatedSessionable->id,
                ],
                [
                    'date_first_view' => now(),
                    'is_completed' => 1,
                    'score' => 100,
                ]
            );
        }

        $otherSessionable = Sessionable::where('session_id', '!=', $sessionable->session_id)->firstOrFail();
        Workout::create([
            'participant_id' => 3,
            'sessionable_id' => $otherSessionable->id,
            'date_first_view' => now(),
            'is_completed' => 0,
            'score' => 0,
        ]);

        $response = $this->get(route('learningCourse', ['participant' => 3]));

        $response->assertSee('style="width: 100%"', false);
    }
}
