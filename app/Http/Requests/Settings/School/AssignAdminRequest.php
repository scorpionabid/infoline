<?php

namespace App\Http\Requests\Settings\School;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization will be handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'school_id' => [
                'required',
                'integer',
                'exists:schools,id'
            ],
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::unique('school_admins', 'user_id')->where(function ($query) {
                    return $query->where('school_id', $this->school_id)
                                ->where('status', 1);
                })
            ]
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'school_id.required' => 'Məktəb ID-si tələb olunur',
            'school_id.exists' => 'Seçilmiş məktəb mövcud deyil',
            
            'user_id.required' => 'İstifadəçi ID-si tələb olunur',
            'user_id.exists' => 'Seçilmiş istifadəçi mövcud deyil',
            'user_id.unique' => 'Bu istifadəçi artıq bu məktəbin adminidir'
        ];
    }
}