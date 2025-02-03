<?php

namespace App\Http\Requests\API\V1\Auth;

use App\Domain\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()],
            'utis_code' => ['required', 'string', 'size:7', 'unique:users'],
            'user_type' => ['required', 'string', 'in:' . implode(',', array_column(UserType::cases(), 'value'))],
            'region_id' => ['required', 'exists:regions,id'],
            'sector_id' => ['required', 'exists:sectors,id'],
            'school_id' => ['required_if:user_type,' . UserType::SCHOOL_ADMIN->value, 'exists:schools,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'utis_code.size' => 'The UTIS code must be exactly 7 characters.',
            'school_id.required_if' => 'The school ID is required for school administrators.',
            'email.unique' => 'The email has already been taken.',
            'username.unique' => 'The username has already been taken.',
        ];
    }
}
