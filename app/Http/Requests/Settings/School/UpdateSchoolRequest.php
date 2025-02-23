<?php

namespace App\Http\Requests\Settings\School;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'utis_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('schools', 'utis_code')->ignore($this->school),
                'regex:/^[A-Za-z0-9]+$/'
            ],
            'type' => [
                'required',
                Rule::in(array_keys(config('enums.school_types')))
            ],
            'region_id' => [
                'required',
                'integer',
                'exists:regions,id'
            ],
            'sector_id' => [
                'required',
                'integer',
                'exists:sectors,id'
            ],
            'phone' => [
                'nullable',
                'string',
                'regex:/^\+?994[0-9]{9}$/'
            ],
            'email' => [
                'nullable',
                'email',
                'max:255'
            ],
            'address' => [
                'nullable',
                'string',
                'max:500'
            ],
            'admin_id' => [
                'nullable',
                'integer',
                'exists:users,id'
            ]
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Məktəbin adı tələb olunur',
            'name.max' => 'Məktəbin adı 255 simvoldan çox ola bilməz',
            
            'utis_code.required' => 'UTİS kodu tələb olunur',
            'utis_code.unique' => 'Bu UTİS kodu artıq mövcuddur',
            'utis_code.regex' => 'UTİS kodu yalnız hərf və rəqəmlərdən ibarət olmalıdır',
            
            'type.required' => 'Məktəb tipi tələb olunur',
            'type.in' => 'Yanlış məktəb tipi',
            
            'region_id.required' => 'Region tələb olunur',
            'region_id.exists' => 'Seçilmiş region mövcud deyil',
            
            'sector_id.required' => 'Sektor tələb olunur',
            'sector_id.exists' => 'Seçilmiş sektor mövcud deyil',
            
            'phone.regex' => 'Telefon nömrəsi düzgün formatda deyil. Nümunə: +994501234567',
            
            'email.email' => 'Email düzgün formatda deyil',
            'email.max' => 'Email 255 simvoldan çox ola bilməz',
            
            'address.max' => 'Ünvan 500 simvoldan çox ola bilməz',
            
            'admin_id.exists' => 'Seçilmiş admin mövcud deyil'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('phone')) {
            // Remove all spaces and format phone number
            $phone = preg_replace('/\s+/', '', $this->phone);
            if (!str_starts_with($phone, '+')) {
                $phone = '+' . $phone;
            }
            $this->merge(['phone' => $phone]);
        }
    }
}