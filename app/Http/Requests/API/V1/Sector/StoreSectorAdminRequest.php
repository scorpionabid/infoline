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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|unique:users,username|alpha_dash|max:50',
            'password' => 'required|min:8',
            'utis_code' => 'required|string|size:7|unique:users',
            'sector_id' => 'required|exists:sectors,id',
            'user_type' => 'required|in:sectoradmin'
        ];
    }

    public function messages(): array
    {
        return [    
            'first_name.required' => 'Ad daxil edilməlidir',
            'last_name.required' => 'Soyad daxil edilməlidir',
            'email.required' => 'Email daxil edilməlidir',
            'email.unique' => 'Bu email artıq istifadə olunub',
            'username.required' => 'İstifadəçi adı daxil edilməlidir',
            'username.unique' => 'Bu istifadəçi adı artıq mövcuddur',
            'username.alpha_dash' => 'Istifadəçi adı sadece alfanumerik və - və _ simvollarından ibarət olmalıdır',
            'password.required' => 'Şifrə daxil edilməlidir',
            'password.min' => 'Şifrə 8 rəqəmdən az olmamalıdır',
            'utis_code.required' => 'UTİS kodu daxil edilməlidir',
            'utis_code.size' => 'UTİS kodu 7 rəqəmdən ibarət olmalıdır',
            'utis_code.unique' => 'Bu UTİS kodu artıq istifadə olunub',
            'sector_id.required' => 'Sektor seçilməlidir',
            'sector_id.exists' => 'Seçilmiş sektor mövcud deyil',
            'user_type.required' => 'İstifadəçi tipi təyin edilməlidir',
            'user_type.in' => 'İstifadəçi tipi düzgün deyil'
        ];
    }
}