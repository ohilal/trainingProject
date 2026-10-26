<?php

namespace App\Utility\Modules\Terms;

use App\Models\Participant;
use App\Models\User;
use App\Models\Workout;
use App\Models\Term;
use Illuminate\Database\Eloquent\Collection;

class ParticipantInfoGenerator
{

    /**
     * getLastTermForParticipant
     *
     * @param  User|null $participant
     * @return Term|null
     */
    public static function getLastTermForParticipant(User $user)
    {
        $terms = $user->Terms;
        if (empty($terms))
            return null;

        $lastTerm = $terms->sortByDesc('created_at')->first();
        return $lastTerm;
        
    }

    /**
     * getTermStatistic
     *
     * @param  mixed $participant
     * @param  mixed $is_mentor
     * @return Participant
     */
    public static function getTermStatistic(Participant $participant, bool $is_mentor = false): Participant
    {
        $participant =  Participant::with('User', 'Workout', 'Term.Sessions.Related')
            ->find($participant->id);


        SessionInfoGenerator::getSessionStatistic($participant, $is_mentor);

        return $participant;
    }

    /**
     * getAllTermsForParticipant
     *
     * @param  mixed $user
     * @param  mixed $is_mentor
     * @return Collection
     */
    public static function getAllTermsForParticipant(User $user, bool $is_mentor = false): Collection
    {
        $terms = Participant::with('Term.Department', 'Term.Course', 'Term.Sessions.Related', 'Workout')
            ->where('user_id', $user->id)
            ->get();

        foreach ($terms as $term) {
            $term->Term->total_task = $term->Term->all_activities->count();
            $term->Term->task_done = $term->Workout->where('is_completed', '1')->count();
            $term->next_activity = self::getNextActivity($term);
        }

        $terms->is_mentor = $is_mentor;
        return $terms;
    }

    private static function getNextActivity(Participant $participant): ?array
    {
        foreach ($participant->Term->Sessions as $session) {
            foreach ($session->Related as $sessionable) {
                $workout = $participant->Workout->firstWhere('sessionable_id', $sessionable->id);

                if ($workout && $workout->is_completed) {
                    continue;
                }

                $activity = $sessionable->Model;

                if (!$activity) {
                    continue;
                }

                return [
                    'title' => $activity->title ?? class_basename($sessionable->sessionable_type),
                    'url' => route('taskLearner', [
                        'participant' => $participant->id,
                        'sessionable' => $sessionable->id,
                    ]),
                    'session_title' => $session->title,
                    'estimated_minutes' => $activity->duration ?? null,
                ];
            }
        }

        return null;
    }


    /**
     * getLastActivityForUser
     *
     * @param  mixed $user
     * @return Collection
     */
    public static function getLastActivityForUser(User $user): Collection
    {
        $particpantsIds = Participant::where('user_id', $user->id)
            ->pluck('id');

        $lastActivities = Workout::whereIn('participant_id', $particpantsIds)
            ->orderby('created_at', 'desc')
            ->limit(10)
            ->get();

        return $lastActivities;
    }
}
