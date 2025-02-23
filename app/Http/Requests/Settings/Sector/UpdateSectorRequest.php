<?php

namespace App\Http\Requests\Settings\Sector;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSectorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sector = $this->route('sector');
        
        return [
            'region_id' => [
                'required',
                'exists:regions,id',
                Rule::in([$sector->region_id]) // Region dəyişdirilə bilməz
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sectors')->where(function ($query) use ($sector) {
                    return $query->where('region_id', $sector->region_id)
                               ->where('id', '!=', $sector->id);
                })
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'region_id.required' => 'Region seçilməlidir',
            'region_id.exists' => 'Seçilən region mövcud deyil',
            'region_id.in' => 'Region dəyişdirilə bilməz',
            'name.required' => 'Sektor adı daxil edilməlidir',
            'name.string' => 'Sektor adı mətn olmalıdır',
            'name.max' => 'Sektor adı :max simvoldan çox ola bilməz',
            'name.unique' => 'Bu adda sektor artıq mövcuddur'
        ];
    }
}