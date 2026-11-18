<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BadgeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $badgeId = $this->route('badge')?->id;
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'file' => 'required',
            'min_coins' => 'required|integer|min:0|max:28000',
            'max_coins' => 'required|integer|min:0|max:28000',
          'min_coins' => 'required|integer|min:0|max:28000|lt:max_coins',
            'max_coins' => [
                'required',
                'integer',
                'min:1',
                'max:28000',
                function ($attribute, $value, $fail) use ($badgeId) {
                    $min = (int) $this->input('min_coins');
                    // Reject ranges that overlap an existing badge's range.
                    $conflict = \App\Models\Badge::where('id', '!=', $badgeId)
                        ->where('min_coins', '<', $value)
                        ->where('max_coins', '>', $min)
                        ->first();
                    if ($conflict) {
                        $fail(__('This coin range overlaps with the ":badge" badge (:min - :max).', [
                            'badge' => $conflict->title,
                            'min' => $conflict->min_coins,
                            'max' => $conflict->max_coins,
                        ]));
                    }
                },
            ],
        ];
    }
}