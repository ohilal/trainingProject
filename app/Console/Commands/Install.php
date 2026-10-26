<?php

namespace App\Console\Commands;

use App\Models\QuestionType;
use Illuminate\Console\Command;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert the required system records (question types) that ship with the application';

    /**
     * Question types backed by an adapter in app/Utility/Question/Adabpter.
     * These are required system configuration, not dummy/example content.
     *
     * @var array<int, array<string, mixed>>
     */
    private array $requiredQuestionTypes = [
        ['title' => 'TestQuestion', 'icon' => '', 'is_mentor' => false],
        ['title' => 'TrueFalseQuestion', 'icon' => '', 'is_mentor' => false],
        ['title' => 'MultipleQuestion', 'icon' => '', 'is_mentor' => false],
        ['title' => 'EssayQuestion', 'icon' => '', 'is_mentor' => true],
        ['title' => 'UploadFileQuestion', 'icon' => '', 'is_mentor' => true],
        ['title' => 'ShortAnswerQuestion', 'icon' => '', 'is_mentor' => true],
        ['title' => 'MatchingCaseQuestion', 'icon' => '', 'is_mentor' => false],
        ['title' => 'VoiceRecordQuestion', 'icon' => '', 'is_mentor' => true],
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $created = 0;

        foreach ($this->requiredQuestionTypes as $questionType) {
            // Idempotent: only insert rows that don't exist yet, so this is
            // safe to run on a database that already has data.
            if (!QuestionType::where('title', $questionType['title'])->exists()) {
                QuestionType::create($questionType);
                $created++;
            }
        }

        $this->info("Question types: {$created} created, "
            . (count($this->requiredQuestionTypes) - $created) . ' already present.');

        return self::SUCCESS;
    }
}
