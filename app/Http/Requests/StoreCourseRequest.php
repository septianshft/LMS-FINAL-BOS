<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['admin','trainer']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'about' => 'required|string',
            'slug' => 'nullable|string|unique:courses,slug',
            'thumbnail' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'course_mode_id' => 'required|exists:course_modes,id',
            'course_level_id' => 'required|exists:course_levels,id',
            'trainer_id' => 'nullable|exists:trainers,id',
            'course_keypoints.*' => 'nullable|string|max:255',
            'path_trailer' => 'required|string|max:255',
            'enrollment_start' => 'nullable|date',
            'enrollment_end' => 'nullable|date|after_or_equal:enrollment_start',

        ];
    }
    
}
