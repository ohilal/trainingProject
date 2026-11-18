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
        'file' => [
            'required',
            'max:1024', // 1MB Max
            'mimes:jpg,jpeg,png,gif,webp,svg',
        ],
    ];


    protected array $files = [
        'file' => 'required|file|max:921600',
    ];

    protected array $public = [
        'file' => 'required', // 1MB Max
    ];


    public function getTargetRule(string $target): array
    {
        $rules = isset($this->$target) ? $this->$target : $this->public;

        if ($target === 'badges') {
            $rules['file'][] = function (string $attribute, $value, callable $fail): void {
                if (!$value instanceof \Illuminate\Http\UploadedFile) {
                    return;
                }

                if (strtolower($value->getClientOriginalExtension()) !== 'svg') {
                    return;
                }

                $content = (string) file_get_contents($value->getRealPath());

                if (stripos($content, '<svg') === false) {
                    $fail(__('validation.image', ['attribute' => $attribute]));
                }
            };
        }

        return $rules;
    }
}
