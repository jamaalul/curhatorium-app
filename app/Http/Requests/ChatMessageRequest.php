<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatMessageRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'session_id' => 'required|string|exists:chat_sessions,session_id',
            'message' => 'required|string|max:1000|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'session_id.required' => 'Session ID is required.',
            'session_id.exists' => 'Invalid session ID.',
            'message.required' => 'Message cannot be empty.',
            'message.max' => 'Message cannot exceed 1000 characters.',
            'message.min' => 'Message cannot be empty.',
        ];
    }
} 