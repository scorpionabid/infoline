<?php

namespace App\Http\Requests\API\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email tələb olunur',
            'email.email' => 'Email düzgün formatda deyil',
            'password.required' => 'Şifrə tələb olunur'
        ];
    }
}
