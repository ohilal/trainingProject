<?php

namespace App\View\Components\Atoms;

use Illuminate\View\Component;

class Stars extends Component
{

    public int $score;
    public float $rating;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(int $score)
    {
        $this->score = $score;
        $this->rating = round(max(0, min(100, $score)) / 10) / 2;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.atoms.stars');
    }
}
