<?php

namespace App\Http\Livewire\Factory\Question\MultipleQuestion;

use App\Http\Livewire\Factory\Question\QuestionComponents;

class Create extends QuestionComponents
{


    public function mount(): void
    {
        $this->answers = ['', '', '', ''];
        $this->correctAnswer = ['', '', '', ''];
        if (!empty($this->question)) {
            $this->setValueWithQuestion();
        }
    }

    public function addNewAnswer(): void
    {
        parent::addNewAnswer();
        $this->correctAnswer[] = '';
    }

    public function removeAnswer($index): void
    {
        $selectedAnswers = [];
        foreach ($this->answers as $answerIndex => $answer) {
            if ((int) $answerIndex !== (int) $index) {
                $selectedAnswers[] = ($this->correctAnswer[$answerIndex] ?? null) === 'answer-' . $answerIndex;
            }
        }

        parent::removeAnswer($index);

        $this->correctAnswer = [];
        foreach ($selectedAnswers as $answerIndex => $isSelected) {
            $this->correctAnswer[$answerIndex] = $isSelected ? 'answer-' . $answerIndex : '';
        }
    }

    /**
     * render
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render()
    {
        return view('livewire.factory.question.multiple-question.create');
    }
}
