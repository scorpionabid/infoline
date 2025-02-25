<?php

namespace App\Http\Requests\Settings\Personal\School;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolAdminRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $adminId = $this->route('admin')->id;

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', "unique:users,email,{$adminId}"],
            'phone' => 'required|string|max:20',
            'utis_code' => ['required', 'string', 'size:7', "unique:users,utis_code,{$adminId}", 'regex:/^\d{7}$/'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Ad daxil edilməlidir',
            'first_name.max' => 'Ad 255 simvoldan çox ola bilməz',
            'last_name.required' => 'Soyad daxil edilməlidir',
            'last_name.max' => 'Soyad 255 simvoldan çox ola bilməz',
            'email.required' => 'Email daxil edilməlidir',
            'email.email' => 'Email düzgün formatda deyil',
            'email.max' => 'Email 255 simvoldan çox ola bilməz',
            'email.unique' => 'Bu email artıq qeydiyyatdan keçib',
            'phone.required' => 'Telefon daxil edilməlidir',
            'phone.max' => 'Telefon 20 simvoldan çox ola bilməz',
            'utis_code.required' => 'UTİS kodu Mütləqdir',
            'utis_code.size' => 'UTİS kodu 7 rəqəmdən ibarət olmalıdır',
            'utis_code.unique' => 'Bu UTİS kodu artıq istifadə olunub',
            'utis_code.regex' => 'UTİS kodu yalnız rəqəmlərdən ibarət olmalıdır',
        ];
    }
}
