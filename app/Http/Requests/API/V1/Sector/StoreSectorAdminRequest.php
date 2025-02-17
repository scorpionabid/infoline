<?php

namespace App\Http\Requests\API\V1\Sector;

use Illuminate\Foundation\Http\FormRequest;

class StoreSectorAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->user_type === 'superadmin';
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'sector_id' => 'required|exists:sectors,id'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Ad daxil edilməlidir',
            'username.required' => 'İstifadəçi adı daxil edilməlidir',
            'username.unique' => 'Bu istifadəçi adı artıq mövcuddur',
            'email.required' => 'Email daxil edilməlidir',
            'email.unique' => 'Bu email artıq istifadə olunub',
            'password.required' => 'Şifrə daxil edilməlidir',
            'sector_id.required' => 'Sektor seçilməlidir',
            'sector_id.exists' => 'Seçilmiş sektor mövcud deyil'
        ];
    }
}