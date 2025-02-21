@component('mail::message')
# Hörmətli {{ $full_name }}

Siz {{ $region }} regionuna admin təyin edildiniz. Sistemə giriş məlumatlarınız:

**Email:** {{ $email }}  
**Şifrə:** {{ $password }}

@component('mail::button', ['url' => route('login')])
Sistemə Giriş
@endcomponent

Təhlükəsizlik məqsədilə sistemə ilk dəfə daxil olduqdan sonra şifrənizi dəyişməyiniz tövsiyə olunur.

Hörmətlə,  
{{ config('app.name') }}
@endcomponent