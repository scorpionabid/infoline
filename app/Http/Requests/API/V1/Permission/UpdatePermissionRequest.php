<?php

namespace App\Http\Requests\API\V1\Permission;

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
            'slug' => ['required', 'string', 'max:255', Rule::unique('permissions')->ignore($this->permission)],
            'description' => ['nullable', 'string'],
            'group' => ['required', 'string', 'max:255']
        ];
    }
}