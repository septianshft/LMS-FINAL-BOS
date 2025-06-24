<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreModuleTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['admin','trainer']);
    }

    public function rules(): array
    {
        return [
            'course_module_id' => 'required|exists:course_modules,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'deadline' => 'nullable|date|after:today',
        ];
    }
}
