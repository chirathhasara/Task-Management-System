<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'in:pending,completed'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required.',
            'title.max' => 'Title must not exceed 255 characters.',
            'status.in' => 'Status must be either pending or completed.',
            'due_date.date' => 'Please provide a valid date.',
            'due_date.after_or_equal' => 'Due date cannot be in the past.',
        ];
    }
}
