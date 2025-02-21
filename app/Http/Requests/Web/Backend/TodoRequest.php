<?php

namespace App\Http\Requests\Web\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TodoRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array {
        $todoId = $this->route('todo') ? $this->route('todo')->id : null;

        return [
            'title'       => 'required|string|min:2|max:255',
            'email'       => 'required|email|unique:todos,email,' . $todoId,
            'due_date'    => 'required|date|after_or_equal:today',
            'description' => 'nullable|string',
        ];
    }
}
