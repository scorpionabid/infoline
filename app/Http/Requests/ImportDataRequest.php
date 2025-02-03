<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:10240' // 10MB limit
            ],
            'school_id' => [
                'required',
                'exists:schools,id'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Excel faylı yüklənməlidir',
            'file.file' => 'Yüklənən fayl düzgün deyil',
            'file.mimes' => 'Fayl Excel formatında olmalıdır (xlsx və ya xls)',
            'file.max' => 'Fayl həcmi 10MB-dan çox olmamalıdır',
            'school_id.required' => 'Məktəb ID-si tələb olunur',
            'school_id.exists' => 'Göstərilən məktəb mövcud deyil'
        ];
    }
}