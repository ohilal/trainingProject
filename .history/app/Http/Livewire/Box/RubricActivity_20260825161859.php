<?php

namespace App\Http\Livewire\Box;

use App\Models\Rubric;
use Livewire\Component;
use Livewire\WithPagination;

class RubricActivity extends Component
{
    use WithPagination;
    protected string $paginationTheme = 'bootstrap';
    protected string $pageName = 'rubricPage';

    public $session;


    /**
     * render
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render()
    {
        $rubrics = Rubric::paginate(15, ['*'], $this->pageName);
        return view('livewire.box.rubric-activity', compact('rubrics'));
    }
}
