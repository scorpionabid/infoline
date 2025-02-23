<?php

namespace App\Http\Requests\Settings\Sector;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSectorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'region_id' => 'required|exists:regions,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sectors')->where(function ($query) {
                    return $query->where('region_id', $this->region_id);
                })
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'region_id.required' => 'Region seçilməlidir',
            'region_id.exists' => 'Seçilən region mövcud deyil',
            'name.required' => 'Sektor adı daxil edilməlidir',
            'name.string' => 'Sektor adı mətn olmalıdır',
            'name.max' => 'Sektor adı :max simvoldan çox ola bilməz',
            'name.unique' => 'Bu adda sektor artıq mövcuddur'
        ];
    }
}