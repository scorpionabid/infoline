<?php

namespace App\Http\Requests\Settings\Personal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSectorRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasRole('superadmin');
    }

    public function rules()
    {
        return [
            'region_id' => 'required|exists:regions,id',
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('sectors', 'name')->ignore($this->route('sector'))
            ],
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('sectors', 'code')->ignore($this->route('sector'))
            ],
            'status' => 'boolean'
        ];
    }

    public function attributes()
    {
        return [
            'region_id' => 'Region',
            'name' => 'Sektor adı',
            'phone' => 'Telefon',
            'description' => 'Təsvir',
            'code' => 'Kod',
            'status' => 'Status'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Sektor adı tələb olunur',
            'name.min' => 'Sektor adı minimum 2 simvol olmalıdır',
            'name.max' => 'Sektor adı maksimum 255 simvol ola bilər',
            'name.unique' => 'Bu adda sektor artıq mövcuddur',
            'region_id.required' => 'Region seçilməlidir',
            'region_id.exists' => 'Seçilmiş region mövcud deyil',
            'phone.max' => 'Telefon nömrəsi maksimum 20 simvol ola bilər',
            'description.max' => 'Təsvir maksimum 1000 simvol ola bilər',
            'code.unique' => 'Bu kod artıq istifadə olunub',
            'code.max' => 'Kod maksimum 50 simvol ola bilər'
        ];
    }
}