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
            'password' => ['required', 'string', 'min:6'],
            'remember' => ['boolean']
        ];
    }

    /**
     * Xəta mesajları
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email ünvanını daxil edin.',
            'email.email' => 'Düzgün email formatı daxil edin.',
            'email.string' => 'Email mətn formatında olmalıdır.',
            'password.required' => 'Şifrəni daxil edin.',
            'password.string' => 'Şifrə mətn formatında olmalıdır.',
            'password.min' => 'Şifrə ən azı :min simvoldan ibarət olmalıdır.'
        ];
    }

    /**
     * Validation attributlarının adları
     */
    public function attributes(): array
    {
        return [
            'email' => 'Email',
            'password' => 'Şifrə',
            'remember' => 'Məni xatırla'
        ];
    }
}