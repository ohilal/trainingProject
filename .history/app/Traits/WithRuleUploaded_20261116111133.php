<?php

namespace App\Traits;

trait WithRuleUploaded
{

    protected array $badges = [
           // Laravel's `image` rule rejects SVG by design (it validates against
        // getimagesize(), which has no SVG signature). Since badge icons are
        // usually designed as vectors, we replace the rule with an explicit
        // extension whitelist plus a content sniff for SVGs.
        //
        // NOTE: an uploaded .svg is served back to the browser from the public
        // disk and can execute scripts when opened directly. Keep this behind
        // the authenticated admin area, or sanitize the XML before storing it.
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
