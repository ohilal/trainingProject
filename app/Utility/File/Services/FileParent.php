<?php

namespace App\Utility\File\Services;

use Illuminate\Support\Facades\Storage;

abstract class FileParent
{

    protected $file;
    protected string $view;


    private function temporayUrlGenerator($file): string
    {
        $relativePath = preg_replace('#^public/#', '', $file);

        if (Storage::disk('public')->missing($relativePath)) {
            return '';
        }

        return asset(Storage::disk('public')->url($relativePath));
    }

    public function makeRenderFile()
    {
        return view($this->view, [
            'url' => $this->temporayUrlGenerator($this->file->file),
            'file' => $this->file
        ])->render();
    }
}
