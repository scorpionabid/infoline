<?php

namespace App\Http\Requests\API\V1\Sector;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSectorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => ['sometimes', 'required', 'string', 'regex:/^\+[1-9]\d{1,14}$/'],
            'region_id' => ['sometimes', 'required', 'integer', 'exists:regions,id'],
        ];
    }
}
