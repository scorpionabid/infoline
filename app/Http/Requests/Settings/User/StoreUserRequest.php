<?php
// StoreUserRequest.php
namespace App\Http\Requests\Settings\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->isSuperAdmin();
    }

    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'username' => ['required', 'string', 'max:50', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'utis_code' => ['required', 'string', 'size:7', 'unique:users'],
            'user_type' => ['required', 'in:sectoradmin,schooladmin'],
            'region_id' => ['required_if:user_type,sectoradmin', 'exists:regions,id'],
            'sector_id' => ['required_if:user_type,schooladmin', 'exists:sectors,id'],
            'school_id' => ['required_if:user_type,schooladmin', 'exists:schools,id'],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id']
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'Ad daxil edilməlidir',
            'last_name.required' => 'Soyad daxil edilməlidir',
            'email.required' => 'Email daxil edilməlidir',
            'email.email' => 'Email düzgün formatda deyil',
            'email.unique' => 'Bu email artıq istifadə olunur',
            'username.required' => 'İstifadəçi adı daxil edilməlidir',
            'username.unique' => 'Bu istifadəçi adı artıq istifadə olunur',
            'password.required' => 'Şifrə daxil edilməlidir',
            'password.min' => 'Şifrə minimum 8 simvol olmalıdır',
            'utis_code.required' => 'UTIS kodu daxil edilməlidir',
            'utis_code.size' => 'UTIS kodu 7 simvol olmalıdır',
            'utis_code.unique' => 'Bu UTIS kodu artıq istifadə olunur',
            'user_type.required' => 'İstifadəçi tipi seçilməlidir',
            'user_type.in' => 'Yanlış istifadəçi tipi',
            'region_id.required_if' => 'Region seçilməlidir',
            'sector_id.required_if' => 'Sektor seçilməlidir',
            'school_id.required_if' => 'Məktəb seçilməlidir',
            'roles.required' => 'Ən azı bir rol seçilməlidir'
        ];
    }
}

// UpdateUserRequest.php
namespace App\Http\Requests\Settings\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->isSuperAdmin();
    }

    public function rules()
    {
        $userId = $this->route('user')->id;

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($userId)],
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8'],
            'utis_code' => ['required', 'string', 'size:7', Rule::unique('users')->ignore($userId)],
            'user_type' => ['required', 'in:sectoradmin,schooladmin'],
            'region_id' => ['required_if:user_type,sectoradmin', 'exists:regions,id'],
            'sector_id' => ['required_if:user_type,schooladmin', 'exists:sectors,id'],
            'school_id' => ['required_if:user_type,schooladmin', 'exists:schools,id'],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id']
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'Ad daxil edilməlidir',
            'last_name.required' => 'Soyad daxil edilməlidir',
            'email.required' => 'Email daxil edilməlidir',
            'email.email' => 'Email düzgün formatda deyil',
            'email.unique' => 'Bu email artıq istifadə olunur',
            'username.required' => 'İstifadəçi adı daxil edilməlidir',
            'username.unique' => 'Bu istifadəçi adı artıq istifadə olunur',
            'password.min' => 'Şifrə minimum 8 simvol olmalıdır',
            'utis_code.required' => 'UTIS kodu daxil edilməlidir',
            'utis_code.size' => 'UTIS kodu 7 simvol olmalıdır',
            'utis_code.unique' => 'Bu UTIS kodu artıq istifadə olunur',
            'user_type.required' => 'İstifadəçi tipi seçilməlidir',
            'user_type.in' => 'Yanlış istifadəçi tipi',
            'region_id.required_if' => 'Region seçilməlidir',
            'sector_id.required_if' => 'Sektor seçilməlidir', 
            'school_id.required_if' => 'Məktəb seçilməlidir',
            'roles.required' => 'Ən azı bir rol seçilməlidir'
        ];
    }
}