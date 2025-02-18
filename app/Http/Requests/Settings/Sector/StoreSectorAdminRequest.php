<?php

namespace App\Http\Requests\Settings\Sector;

use Illuminate\Foundation\Http\FormRequest;

class StoreSectorAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|unique:users,username|alpha_dash|max:50',
            'password' => 'required|min:8',
            'utis_code' => 'required|string|size:7|unique:users',
            'sector_id' => 'required|exists:sectors,id'
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Ad daxil edilməlidir',
            'last_name.required' => 'Soyad daxil edilməlidir',
            'email.unique' => 'Bu email artıq istifadə olunub',
            'username.unique' => 'Bu istifadəçi adı artıq mövcuddur',
            'password.min' => 'Şifrə 8 rəqəmdən az olmamalıdır',
            'utis_code.size' => 'UTİS kodu 7 rəqəmdən ibarət olmalıdır',
            'utis_code.unique' => 'Bu UTİS kodu artıq istifadə olunub',
            'sector_id.required' => 'Sektor seçilməlidir',
            'sector_id.exists' => 'Seçilmiş sektor mövcud deyil'
        ];
    }
}