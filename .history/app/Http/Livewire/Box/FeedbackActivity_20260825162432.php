<?php

namespace App\Http\Livewire\Box;

use App\Models\Feedback;
use Livewire\Component;
use Livewire\WithPagination;

class FeedbackActivity extends Component
{
    use WithPagination;
    protected string $paginationTheme = 'bootstrap';
    public $feedbackPage = 1;
    
    public $session;

    /**
     * render
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render()
    {
        $feedbacks = Feedback::paginate(15, ['*'], 'feedbackPage');
        return view('livewire.box.feedback-activity', compact('feedbacks'));
    }
}
