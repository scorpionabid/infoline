<?php

namespace App\Http\Requests\API\V1\School;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:schools,email'],
            'sector_id' => ['required', 'exists:sectors,id'],
            'utis_code' => ['required', 'string', 'unique:schools,utis_code'],
            'phone' => ['required', 'string', 'regex:/^\+[1-9]\d{1,14}$/'],
            // digər validation qaydaları
        ];
    }

    public function messages(): array
    {
        return [
            'utis_code.required' => 'UTİS kod tələb olunur',
            'utis_code.unique' => 'Bu UTİS kod artıq istifadə olunub',
            'email.unique' => 'Bu email artıq istifadə olunub',
            'phone.required' => 'Telefon nömrəsi tələb olunur',
            'phone.regex' => 'Düzgün telefon nömrəsi daxil edin',
            // digər xəta mesajları
        ];
    }
}
