<?php

namespace App\View\Components\Box;

use App\Models\User;
use App\Utility\Modules\Terms\ParticipantInfoGenerator;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class ProfileReviewBox extends Component
{
    public array $courseProgress = [];

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(?User $user = null)
    {
        $user = $user ?? Auth::user();

        if ($user) {
            $this->courseProgress = ParticipantInfoGenerator::getCourseProgressForParticipant($user);
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.box.profile-review-box');
    }
}
