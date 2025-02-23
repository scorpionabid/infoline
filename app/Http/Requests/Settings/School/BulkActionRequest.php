<?php

namespace App\Http\Requests\Settings\School;

use Illuminate\Foundation\Http\FormRequest;

class BulkActionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('manage-schools');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', 'string', 'in:activate,deactivate,delete'],
            'school_ids' => ['required', 'array', 'min:1'],
            'school_ids.*' => ['required', 'integer', 'exists:schools,id']
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'action.required' => 'Əməliyyat növü tələb olunur',
            'action.in' => 'Yanlış əməliyyat növü',
            'school_ids.required' => 'Məktəblər seçilməyib',
            'school_ids.array' => 'Məktəblər düzgün formatda deyil',
            'school_ids.min' => 'Ən azı bir məktəb seçilməlidir',
            'school_ids.*.required' => 'Məktəb ID-si tələb olunur',
            'school_ids.*.integer' => 'Məktəb ID-si rəqəm olmalıdır',
            'school_ids.*.exists' => 'Seçilmiş məktəb mövcud deyil'
        ];
    }
}
