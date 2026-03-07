<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:pending,completed'],
            'due_date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required.',
            'title.max' => 'Title must not exceed 255 characters.',
            'status.in' => 'Status must be either pending or completed.',
            'due_date.date' => 'Please provide a valid date.',
        ];
    }
}
