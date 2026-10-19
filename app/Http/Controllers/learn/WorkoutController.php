<?php

namespace App\Http\Controllers\learn;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\Question;
use App\Models\Sessionable;
use App\Models\Workout;
use App\Utility\Modules\Tasks\TaskFactory;
use App\Utility\Question\QuestionFactory;
use App\Utility\Workout\WorkoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkoutController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function prepared(Participant $participant, Sessionable $sessionable)
    {

        WorkoutService::WorkOutSyncForThisExcersice($participant, $sessionable, Auth::user());

        return redirect(route('taskLearner', ['participant' => $participant, 'sessionable' => $sessionable]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function task(Participant $participant, Sessionable $sessionable)
    {
        
        $className = $sessionable->sessionable_type;
        
        $task = TaskFactory::Build($className);
        $task->set_user(Auth::user());
        return $task->Render($participant, $sessionable);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function completedAndNext(Workout $workout)
    {
        if (!$workout->is_completed) {
            $workout->update([
                'is_completed' => 1,
                'score' => 100,
                'date_get_score' => now()->toDateString(),
            ]);
        }

        $sessionable = Sessionable::findOrFail($workout->sessionable_id);
        $participant = Participant::findOrFail($workout->participant_id);

        $sessionables = Sessionable::where('session_id', $sessionable->session_id)
            ->orderBy('order')
            ->orderBy('id')
            ->get();
        $currentSessionableIndex = $sessionables->search(function ($item) use ($sessionable) {
            return (int) $item->id === (int) $sessionable->id;
        });

        if ($currentSessionableIndex !== false && $sessionables->has($currentSessionableIndex + 1)) {
            $nextSessionable = $sessionables->get($currentSessionableIndex + 1);

            return redirect()->route('taskLearner', [
                'participant' => $participant->id,
                'sessionable' => $nextSessionable->id,
            ]);
        }

        $sessions = $participant->Term->Sessions;
        $currentSessionIndex = $sessions->search(function ($session) use ($sessionable) {
            return (int) $session->id === (int) $sessionable->session_id;
        });

        if ($currentSessionIndex !== false) {
            foreach ($sessions->slice($currentSessionIndex + 1) as $nextSession) {
                $nextSessionable = $nextSession->Related->first();

                if ($nextSessionable) {
                    return redirect()->route('taskLearner', [
                        'participant' => $participant->id,
                        'sessionable' => $nextSessionable->id,
                    ]);
                }
            }
        }

        return redirect()->route('learningCourse', ['participant' => $participant->id]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function workout(Request $request)
    {

        $request->validate([
            'question_id' => 'required|int',
            'workout_id' => 'required|int'
        ]);


        $question = Question::findorfail($request->question_id);
        $workout = Workout::findorfail($request->workout_id);

        $result =  QuestionFactory::Build($question->questionType)
            ->workoutChecker($question, $workout, $request);

        return response()->json($result);
    }
}
