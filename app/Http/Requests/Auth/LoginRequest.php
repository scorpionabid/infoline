<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Request-in icazəli olub-olmadığını müəyyən edir
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation qaydaları
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['boolean']
        ];
    }

    /**
     * Xəta mesajları
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email daxil edilməlidir.',
            'email.email' => 'Düzgün email formatı daxil edin.',
            'password.required' => 'Şifrə daxil edilməlidir.',
        ];
    }
}