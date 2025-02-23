<?php

namespace App\Http\Requests\Settings\School;

use Illuminate\Foundation\Http\FormRequest;

class CreateSchoolAdminRequest extends FormRequest
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
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'username' => ['required', 'string', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8'],
            'utis_code' => ['required', 'string', 'unique:users,utis_code'],
            'phone' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
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
            'email.unique' => 'Bu email artıq istifadə olunub',
            'username.required' => 'İstifadəçi adı daxil edilməlidir',
            'username.unique' => 'Bu istifadəçi adı artıq istifadə olunub',
            'password.required' => 'Şifrə daxil edilməlidir',
            'password.min' => 'Şifrə minimum 8 simvol olmalıdır',
            'utis_code.required' => 'UTİS kodu daxil edilməlidir',
            'utis_code.unique' => 'Bu UTİS kodu artıq istifadə olunub',
        ];
    }
}
