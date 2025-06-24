<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['admin','trainer']);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'order' => 'nullable|integer|min:0',
        ];
    }
}
