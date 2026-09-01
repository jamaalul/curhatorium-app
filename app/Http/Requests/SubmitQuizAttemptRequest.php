<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SubmitQuizAttemptRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'answers' => ['required', 'array', 'min:1'],
            'answers.*' => ['required', 'array:question_id,selected_option_id,answer_text'],
            'answers.*.question_id' => ['required', 'integer', 'distinct'],
            'answers.*.selected_option_id' => ['nullable', 'integer'],
            'answers.*.answer_text' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
