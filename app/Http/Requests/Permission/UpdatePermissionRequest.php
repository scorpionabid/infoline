<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('permissions')->ignore($this->permission)
            ],
            'description' => ['nullable', 'string'],
            'group' => ['required', 'string', 'max:255']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'İcazə adı tələb olunur',
            'name.max' => 'İcazə adı 255 simvoldan çox ola bilməz',
            'slug.required' => 'Slug tələb olunur',
            'slug.unique' => 'Bu slug artıq istifadə olunub',
            'group.required' => 'Qrup adı tələb olunur'
        ];
    }
}