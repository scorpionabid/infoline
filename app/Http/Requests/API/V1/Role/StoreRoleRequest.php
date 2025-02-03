<?php

namespace App\Http\Requests\API\V1\Role;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:roles,slug'],
            'description' => ['nullable', 'string'],
            'is_system' => ['boolean']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Rol adı tələb olunur',
            'name.max' => 'Rol adı 255 simvoldan çox ola bilməz',
            'slug.required' => 'Slug tələb olunur',
            'slug.unique' => 'Bu slug artıq istifadə olunub'
        ];
    }
}