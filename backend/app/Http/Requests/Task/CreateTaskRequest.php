<?php
// app/Http/Requests/Task/CreateTaskRequest.php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class CreateTaskRequest extends FormRequest
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
            'due_date' => 'nullable|date|after_or_equal:today',
            'priority' => 'in:low,medium,high',
            'status' => 'in:todo,in_progress,done',
            'order' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề là bắt buộc',
            'title.max' => 'Tiêu đề không được quá 255 ký tự',
            'due_date.date' => 'Ngày hết hạn không đúng định dạng',
            'due_date.after_or_equal' => 'Ngày hết hạn phải từ hôm nay trở đi',
            'priority.in' => 'Độ ưu tiên phải là low, medium hoặc high',
            'status.in' => 'Trạng thái phải là todo, in_progress hoặc done',
            'order.integer' => 'Thứ tự phải là số nguyên',
            'order.min' => 'Thứ tự phải lớn hơn hoặc bằng 0',
        ];
    }
}
