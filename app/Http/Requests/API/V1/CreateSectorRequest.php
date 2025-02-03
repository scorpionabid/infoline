<?php

namespace App\Http\Requests\API\V1\Sector;

use Illuminate\Foundation\Http\FormRequest;

class StoreSectorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^\+994(50|51|55|70|77|99)[0-9]{7}$/'],
            'region_id' => ['required', 'exists:regions,id'],
        ];
    }
}