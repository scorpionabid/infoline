<?php

namespace App\Http\Requests\Settings\Personal\School;

use App\Domain\Entities\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', [User::class, $this->route('school')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
            'utis_code' => ['required', 'string', 'size:7', 'unique:users,utis_code','regex:/^\d{7}$/'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Ad daxil edilməlidir',
            'first_name.max' => 'Ad 255 simvoldan çox ola bilməz',
            'last_name.required' => 'Soyad daxil edilməlidir',
            'last_name.max' => 'Soyad 255 simvoldan çox ola bilməz',
            'email.required' => 'Email daxil edilməlidir',
            'email.email' => 'Email düzgün formatda deyil',
            'email.unique' => 'Bu email artıq istifadə olunur',
            'phone.required' => 'Telefon daxil edilməlidir',
            'phone.max' => 'Telefon 20 simvoldan çox ola bilməz',
            'password.required' => 'Şifrə daxil edilməlidir',
            'password.min' => 'Şifrə minimum 8 simvol olmalıdır',
            'password.confirmed' => 'Şifrələr uyğun gəlmir',
            'password_confirmation.required' => 'Şifrə təkrarı daxil edilməlidir',
            'utis_code.required' => 'UTİS kodu Mütləqdir',
            'utis_code.size' => 'UTİS kodu 7 rəqəmdən ibarət olmalıdır',
            'utis_code.unique' => 'Bu UTİS kodu artıq istifadə olunub',
            'utis_code.regex' => 'UTİS kodu yalnız rəqəmlərdən ibarət olmalıdır',
            'utis_code' => [
                'required', 
                'string', 
                'size:7', 
                'unique:users,utis_code',
                    'regex:/^\d{7}$/'
            ],
            'username' => [
                'required', 
                'string', 
                'unique:users,username'
            ],
            'school_id' => 'nullable|exists:schools,id'
        ];
    }
}
