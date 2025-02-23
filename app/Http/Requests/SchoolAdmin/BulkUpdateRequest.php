<?php
// app/Http/Requests/SchoolAdmin/BulkUpdateRequest.php

namespace App\Http\Requests\SchoolAdmin;

use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'values' => 'required|array',
            'values.*.column_id' => 'required|exists:columns,id',
            'values.*.value' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'values.required' => 'Məlumatlar tələb olunur',
            'values.array' => 'Yanlış məlumat formatı',
            'values.*.column_id.required' => 'Sütun ID-si tələb olunur',
            'values.*.column_id.exists' => 'Yanlış sütun ID-si',
            'values.*.value.required' => 'Dəyər tələb olunur'
        ];
    }
}