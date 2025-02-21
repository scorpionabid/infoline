<?php

namespace App\Http\Requests\Settings\School;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('schools')->ignore($this->school)
            ],
            'utis_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('schools')->ignore($this->school)
            ],
            'sector_id' => 'required|exists:sectors,id',
            'admin_id' => 'nullable|exists:users,id',
            'phone' => 'nullable|string|max:20',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('schools')->ignore($this->school)
            ],
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'type' => 'required|string|in:' . implode(',', config('enums.school_types')),
            'description' => 'nullable|string|max:1000',
            'status' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Məktəbin adı tələb olunur',
            'name.unique' => 'Bu adda məktəb artıq mövcuddur',
            'utis_code.required' => 'UTİS kodu tələb olunur',
            'utis_code.unique' => 'Bu UTİS kodu artıq istifadə olunur',
            'sector_id.required' => 'Sektor seçilməlidir',
            'sector_id.exists' => 'Seçilmiş sektor mövcud deyil',
            'admin_id.exists' => 'Seçilmiş admin mövcud deyil',
            'email.email' => 'Düzgün email formatı daxil edin',
            'email.unique' => 'Bu email artıq istifadə olunur',
            'website.url' => 'Düzgün website formatı daxil edin',
            'type.required' => 'Məktəb tipi seçilməlidir',
            'type.in' => 'Yanlış məktəb tipi'
        ];
    }
}