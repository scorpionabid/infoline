<?php

namespace App\Http\Requests\Settings\Region;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegionRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasRole('superadmin');
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:regions,name',
            'code' => 'required|string|max:50|unique:regions,code',
            'phone' => 'required|string|max:20|unique:regions,phone',
            'description' => 'nullable|string|max:500'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Region adı daxil edilməlidir',
            'name.max' => 'Region adı :max simvoldan çox ola bilməz',
            'name.unique' => 'Bu region adı artıq mövcuddur',
            
            'code.required' => 'Region kodu daxil edilməlidir',
            'code.max' => 'Region kodu :max simvoldan çox ola bilməz',
            'code.unique' => 'Bu region kodu artıq mövcuddur',
            
            'phone.required' => 'Telefon nömrəsi daxil edilməlidir',
            'phone.max' => 'Telefon nömrəsi :max simvoldan çox ola bilməz',
            'phone.unique' => 'Bu telefon nömrəsi artıq mövcuddur',
            
            'description.max' => 'Təsvir :max simvoldan çox ola bilməz'
        ];
    }
}