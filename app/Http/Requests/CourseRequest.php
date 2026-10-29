<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'department_id' => 'required|int',
            'description' => 'required|string',
            'image' => 'required|string',
            'allow_self_enrollment' => 'sometimes|boolean',
            'author_id' => 'nullable|integer|exists:authors,id',
            'author_name' => 'nullable|required_with:author_image,author_bio,author_organization|string|max:255',
            'author_image' => 'nullable|image|max:2048',
            'author_bio' => 'nullable|string',
            'author_organization' => 'nullable|string|max:255',
        ];
    }
}
