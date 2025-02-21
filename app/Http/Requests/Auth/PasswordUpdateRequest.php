<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class PasswordUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ];
    }

    public function messages()
    {
        return [
            'token.required' => 'Token tələb olunur',
            'email.required' => 'E-poçt ünvanı tələb olunur',
            'email.email' => 'Düzgün e-poçt ünvanı daxil edin',
            'password.required' => 'Yeni şifrə tələb olunur',
            'password.min' => 'Şifrə ən az 8 simvoldan ibarət olmalıdır',
            'password.confirmed' => 'Şifrə təkrarı uyğun gəlmir',
        ];
    }
}