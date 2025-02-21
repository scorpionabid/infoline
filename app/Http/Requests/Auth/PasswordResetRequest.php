<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class PasswordResetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'E-poçt ünvanı tələb olunur',
            'email.email' => 'Düzgün e-poçt ünvanı daxil edin',
            'email.exists' => 'Bu e-poçt ünvanı sistemdə mövcud deyil',
        ];
    }
}