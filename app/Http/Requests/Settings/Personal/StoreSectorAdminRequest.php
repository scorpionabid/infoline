<?php

namespace App\Http\Requests\Settings\Personal;

use Illuminate\Foundation\Http\FormRequest;

class StoreSectorAdminRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasRole('superadmin');
    }

    public function rules()
    {
        return [
            'full_name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'send_credentials' => 'boolean'
        ];
    }

    public function attributes()
    {
        return [
            'full_name' => 'Ad Soyad',
            'email' => 'Email',
            'phone' => 'Telefon',
            'send_credentials' => 'Giriş məlumatlarını göndər'
        ];
    }

    public function messages()
    {
        return [
            'full_name.required' => 'Ad Soyad tələb olunur',
            'full_name.min' => 'Ad Soyad minimum 3 simvol olmalıdır',
            'full_name.max' => 'Ad Soyad maksimum 255 simvol ola bilər',
            'email.required' => 'Email tələb olunur',
            'email.email' => 'Düzgün email formatı daxil edin',
            'email.unique' => 'Bu email artıq istifadə olunub',
            'phone.required' => 'Telefon tələb olunur',
            'phone.max' => 'Telefon nömrəsi maksimum 20 simvol ola bilər'
        ];
    }
}