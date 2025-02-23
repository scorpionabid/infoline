<?php

namespace App\Http\Requests\Settings\Region;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRegionRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->user_type === \App\Domain\Enums\UserType::SUPER_ADMIN->value;
    }

    public function rules()
    {
        $region = $this->route('region');
        
        if (!$region) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Region not found');
        }
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('regions', 'name')->ignore($region->id)
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                \Illuminate\Validation\Rule::unique('regions', 'phone')->ignore($region->id)
            ]
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Region adı daxil edilməlidir',
            'name.string' => 'Region adı mətn olmalıdır',
            'name.max' => 'Region adı :max simvoldan çox ola bilməz',
            'name.unique' => 'Bu region adı artıq mövcuddur',
            
            'phone.string' => 'Telefon nömrəsi mətn olmalıdır',
            'phone.max' => 'Telefon nömrəsi :max simvoldan çox ola bilməz',
            'phone.unique' => 'Bu telefon nömrəsi artıq mövcuddur'
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, response()->json([
            'success' => false,
            'message' => 'Validasiya xətası',
            'errors' => $validator->errors()
        ], 422));
    }
}