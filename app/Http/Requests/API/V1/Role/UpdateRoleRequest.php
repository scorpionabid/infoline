<?php

namespace App\Http\Requests\API\V1\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('roles')->ignore($this->role)
            ],
            'description' => ['nullable', 'string'],
            'is_system' => ['boolean']
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Rol adı tələb olunur',
            'name.max' => 'Rol adı 255 simvoldan çox ola bilməz',
            'slug.required' => 'Slug tələb olunur',
            'slug.unique' => 'Bu slug artıq istifadə olunub',
            'slug.max' => 'Slug 255 simvoldan çox ola bilməz',
            'is_system.boolean' => 'Sistem rol dəyəri boolean tipində olmalıdır'
        ];
    }
}