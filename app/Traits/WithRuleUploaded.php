<?php

namespace App\Traits;

trait WithRuleUploaded
{

    protected array $badges = [
        'file' => 'required|image|max:1024', // 1MB Max
    ];


    protected array $files = [
        'file' => 'required|file|max:921600',
    ];

    protected array $public = [
        'file' => 'required', // 1MB Max
    ];


    public function getTargetRule(string $target): array
    {

        if (isset($this->$target))
            return $this->$target;

        return $this->public;
    }
}
