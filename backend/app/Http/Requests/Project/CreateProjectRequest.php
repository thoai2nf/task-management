<?php
// app/Http/Requests/Project/CreateProjectRequest.php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class CreateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề là bắt buộc',
            'title.max' => 'Tiêu đề không được quá 255 ký tự',
            'status.in' => 'Trạng thái phải là active hoặc inactive',
        ];
    }
}
