<?php

namespace App\Http\Requests\Settings\Region;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegionRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->user_type === \App\Domain\Enums\UserType::SUPER_ADMIN->value;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:regions,name',
            'phone' => 'nullable|string|max:20|unique:regions,phone'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Region adı daxil edilməlidir',
            'name.max' => 'Region adı :max simvoldan çox ola bilməz',
            'name.unique' => 'Bu region adı artıq mövcuddur',
            
            'phone.max' => 'Telefon nömrəsi :max simvoldan çox ola bilməz',
            'phone.unique' => 'Bu telefon nömrəsi artıq mövcuddur'
        ];
    }
}