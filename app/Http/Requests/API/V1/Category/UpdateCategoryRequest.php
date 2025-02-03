<?php

namespace App\Http\Requests\API\V1\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('categories')->ignore($this->category)
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Kateqoriya adı tələb olunur',
            'name.max' => 'Kateqoriya adı 255 simvoldan çox ola bilməz',
            'name.unique' => 'Bu adda kateqoriya artıq mövcuddur'
        ];
    }
}
