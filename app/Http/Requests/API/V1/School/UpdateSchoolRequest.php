<?php

namespace App\Http\Requests\API\V1\School;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'utis_code' => ['required', 'string', 'unique:schools,utis_code,' . $this->route('id')],
            'phone' => ['required', 'string', 'regex:/^\+994\d{9}$/'],
            'email' => ['required', 'email', 'unique:schools,email,' . $this->route('id')],
            'sector_id' => ['required', 'exists:sectors,id']
        ];
    }
}
