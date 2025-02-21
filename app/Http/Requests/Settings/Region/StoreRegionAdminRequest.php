<?php

namespace App\Http\Requests\Settings\Region;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegionAdminRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasRole('superadmin');
    }

    public function rules()
    {
        return [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'send_credentials' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'full_name.required' => 'Ad Soyad daxil edilməlidir',
            'full_name.max' => 'Ad Soyad :max simvoldan çox ola bilməz',
            
            'email.required' => 'Email daxil edilməlidir',
            'email.email' => 'Düzgün email formatı daxil edin',
            'email.unique' => 'Bu email artıq mövcuddur',
            
            'phone.required' => 'Telefon nömrəsi daxil edilməlidir',
            'phone.max' => 'Telefon nömrəsi :max simvoldan çox ola bilməz',
            'phone.unique' => 'Bu telefon nömrəsi artıq mövcuddur'
        ];
    }
}