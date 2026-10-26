<?php

namespace App\Http\Livewire\Factory;

use App\Models\Question;
use App\Utility\Question\QuestionFactory;
use Livewire\Component;
use App\Models\QuestionType;

class Render extends Component
{

    public ?int $questionTypeId = null;
    public string $component = '';
    public ?Question $question = null;
    public $quiz = null;

    public function mount(): void
    {
        // Note: the original code did `QuestionType::first()->id`, which fatals
        // ("Call to a member function id on null") whenever the question_types
        // lookup below returns nothing. That is exactly the 500 seen on
        // /panel/question/create when this app's DB connection points at a
        // different/older database than expected (e.g. a stale .env or cached
        // config on the server). Guard it so we render an empty form instead
        // of crashing.
        try {
            $firstQuestionType = $this->firstQuestionType();
        } catch (\Throwable $e) {
            report($e);
            $firstQuestionType = null;
        }

        if (null !== $firstQuestionType) {
            $this->questionTypeId = $firstQuestionType->id;
        }

        if (!empty($this->question)) {
            $this->questionTypeId = $this->question->question_type_id;

            $this->getComponent($this->questionTypeId);
        }
    }

    /**
     * Resolve the first available question type.
     *
     * Uses orderBy('id') rather than bare first() because some MSSQL setups
     * without ORDER BY in OFFSET/FETCH pagination throw
     * "SQLSTATE[21S01] The ORDER BY clause is incompatible with the
     * distinct-by operator" when Laravel emits `select top 1 ...` against
     * certain queries; an explicit order keeps the query deterministic.
     */
    private function firstQuestionType(): ?QuestionType
    {
        return QuestionType::query()->orderBy('id')->first();
    }


    private function getComponent($questionTypeId): void
    {
        $questionType = QuestionType::find($questionTypeId);

        if (null === $questionType) {
            return;
        }

        // The adapter class is derived from the DB row's title
        // (App\Utility\Question\Adabpter\{title}). If this machine's code
        // doesn't ship an adapter for a title stored in the shared DB,
        // QuestionFactory::build() would throw "Class not found" -> 500.
        // Degrade gracefully instead.
        $adapterClass = "App\\Utility\\Question\\Adabpter\\" . $questionType->title;

        if (!class_exists($adapterClass)) {
            return;
        }

        $this->component = (string)QuestionFactory::build($questionType)->getCreateUpdateForm();
    }

    public function selectQuestionType(): void
    {
        $this->getComponent($this->questionTypeId);
    }


    /**
     * render
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render()
    {
        try {
            $questionTypes = QuestionType::query()->orderBy('id')->get();
        } catch (\Throwable $e) {
            // Never let a DB hiccup (wrong connection, missing table on this
            // machine's resolved DSN, MSSQL ORDER BY quirks) bubble up as a
            // raw 500 — show the page with an empty type list and log why.
            report($e);
            $questionTypes = collect();
        }

        return view('livewire.factory.render', compact("questionTypes"));
    }
}
