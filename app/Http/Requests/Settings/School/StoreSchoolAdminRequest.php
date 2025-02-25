<?php

namespace App\Http\Requests\Settings\School;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(['super', 'sector_admin']);
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Ad daxil edilməlidir',
            'first_name.max' => 'Ad 255 simvoldan çox ola bilməz',
            'last_name.required' => 'Soyad daxil edilməlidir',
            'last_name.max' => 'Soyad 255 simvoldan çox ola bilməz',
            'email.required' => 'Email daxil edilməlidir',
            'email.email' => 'Email düzgün formatda deyil',
            'email.unique' => 'Bu email artıq istifadə olunur',
            'phone.required' => 'Telefon daxil edilməlidir',
            'phone.max' => 'Telefon 20 simvoldan çox ola bilməz',
            'password.required' => 'Şifrə daxil edilməlidir',
            'password.min' => 'Şifrə minimum 8 simvol olmalıdır',
            'password.confirmed' => 'Şifrələr uyğun gəlmir',
            'password_confirmation.required' => 'Şifrə təkrarı daxil edilməlidir',
        ];
    }
}
