<?php

namespace App\Http\Requests\Settings\School;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolDataRequest extends FormRequest
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
            'data' => ['required', 'array'],
            'data.*' => ['required', 'string', 'max:1000']
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
            'data.required' => 'Məlumatlar tələb olunur',
            'data.array' => 'Məlumatlar düzgün formatda deyil',
            'data.*.required' => 'Bütün sahələr doldurulmalıdır',
            'data.*.string' => 'Məlumat mətni düzgün formatda deyil',
            'data.*.max' => 'Məlumat mətni :max simvoldan çox ola bilməz'
        ];
    }
}
