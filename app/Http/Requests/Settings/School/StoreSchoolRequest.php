<?php

namespace App\Http\Requests\Settings\School;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSchoolRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2'
            ],
            'utis_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('schools', 'utis_code')->whereNull('deleted_at'),
                'regex:/^[A-Za-z0-9]+$/'
            ],
            'type' => [
                'required',
                'string',
                Rule::in(array_keys(config('enums.school_types')))
            ],
            'sector_id' => [
                'required',
                'integer',
                'exists:sectors,id'
            ],
            'phone' => [
                'nullable',
                'string',
                'regex:/^\+?994[0-9]{9}$/',
                'max:13'
            ],
            'email' => [
                'nullable',
                'email',
                'max:255'
            ],
            'address' => [
                'nullable',
                'string',
                'max:500',
                'min:5'
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
            'name.min' => 'Məktəbin adı ən az 2 simvol olmalıdır',
            
            'utis_code.required' => 'UTİS kodu tələb olunur',
            'utis_code.unique' => 'Bu UTİS kodu artıq mövcuddur',
            'utis_code.regex' => 'UTİS kodu yalnız hərf və rəqəmlərdən ibarət olmalıdır',
            'utis_code.max' => 'UTİS kodu 50 simvoldan çox ola bilməz',
            
            'type.required' => 'Məktəb tipi tələb olunur',
            'type.in' => 'Yanlış məktəb tipi',
            'type.string' => 'Məktəb tipi düzgün formatda deyil',
            
            'sector_id.required' => 'Sektor tələb olunur',
            'sector_id.exists' => 'Seçilmiş sektor mövcud deyil',
            'sector_id.integer' => 'Sektor düzgün formatda deyil',
            
            'phone.regex' => 'Telefon nömrəsi düzgün formatda deyil. Nümunə: +994501234567',
            'phone.max' => 'Telefon nömrəsi 13 simvoldan çox ola bilməz',
            
            'email.email' => 'Email düzgün formatda deyil (nümunə: example@domain.com)',
            'email.max' => 'Email 255 simvoldan çox ola bilməz',
            
            'address.max' => 'Ünvan 500 simvoldan çox ola bilməz',
            'address.min' => 'Ünvan ən az 5 simvol olmalıdır'
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