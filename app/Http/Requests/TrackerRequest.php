<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'mood' => 'required|integer|min:1|max:10',
            'activity' => 'required|string|in:work,exercise,social,hobbies,rest,entertainment,nature,food,health,other',
            'activityExplanation' => 'nullable|string|max:500',
            'energy' => 'required|integer|min:1|max:10',
            'productivity' => 'required|integer|min:1|max:10',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'mood.required' => 'Mood harus diisi.',
            'mood.integer' => 'Mood harus berupa angka.',
            'mood.min' => 'Mood minimal 1.',
            'mood.max' => 'Mood maksimal 10.',
            'activity.required' => 'Aktivitas harus dipilih.',
            'activity.in' => 'Aktivitas tidak valid.',
            'activityExplanation.max' => 'Penjelasan aktivitas maksimal 500 karakter.',
            'energy.required' => 'Energi harus diisi.',
            'energy.integer' => 'Energi harus berupa angka.',
            'energy.min' => 'Energi minimal 1.',
            'energy.max' => 'Energi maksimal 10.',
            'productivity.required' => 'Produktivitas harus diisi.',
            'productivity.integer' => 'Produktivitas harus berupa angka.',
            'productivity.min' => 'Produktivitas minimal 1.',
            'productivity.max' => 'Produktivitas maksimal 10.',
        ];
    }
} 