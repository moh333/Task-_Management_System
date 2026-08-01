<?php

namespace App\Http\Requests\Api\Task;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'project_id'  => [
                'required',
                'integer',
                Rule::exists('projects', 'id')->where(function ($query) {
                    $query->where('user_id', $this->user()?->id)
                        ->whereNull('deleted_at');
                }),
            ],
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority'    => ['required', 'string', new Enum(TaskPriority::class)],
            'status'      => ['required', 'string', new Enum(TaskStatus::class)],
            'due_date'    => 'required|date',
        ];
    }
}
