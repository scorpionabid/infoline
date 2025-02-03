<?php

namespace App\Application\DTOs;

use App\Domain\Enums\UserType;

class UserDTO
{
    public ?string $first_name = null;
    public ?string $last_name = null;
    public ?string $utis_code = null;
    public ?string $email = null;
    public ?string $username = null;
    public ?string $password = null;
    public ?string $user_type = null;
    public ?int $region_id = null;
    public ?int $sector_id = null;
    public ?int $school_id = null;

    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function validate(): array
    {
        $errors = [];

        // Required fields
        if (empty($this->first_name)) {
            $errors['first_name'] = 'Ad tələb olunur';
        }

        if (empty($this->last_name)) {
            $errors['last_name'] = 'Soyad tələb olunur';
        }

        if (empty($this->utis_code)) {
            $errors['utis_code'] = 'UTIS kodu tələb olunur';
        } elseif (!preg_match('/^\d{7}$/', $this->utis_code)) {
            $errors['utis_code'] = 'UTIS kodu 7 rəqəmdən ibarət olmalıdır';
        }

        if (empty($this->email)) {
            $errors['email'] = 'Email tələb olunur';
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Düzgün email formatı daxil edin';
        }

        if (empty($this->username)) {
            $errors['username'] = 'İstifadəçi adı tələb olunur';
        } elseif (strlen($this->username) < 3) {
            $errors['username'] = 'İstifadəçi adı minimum 3 simvol olmalıdır';
        }

        if (empty($this->password)) {
            $errors['password'] = 'Şifrə tələb olunur';
        } elseif (strlen($this->password) < 8) {
            $errors['password'] = 'Şifrə minimum 8 simvol olmalıdır';
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $this->password)) {
            $errors['password'] = 'Şifrə ən azı bir böyük hərf, bir kiçik hərf və bir rəqəm tərkibində olmalıdır';
        }

        if (empty($this->user_type)) {
            $errors['user_type'] = 'İstifadəçi tipi tələb olunur';
        } elseif (!in_array($this->user_type, UserType::values())) {
            $errors['user_type'] = 'Yanlış istifadəçi tipi';
        }

        // Conditional validation based on user type
        if ($this->user_type === UserType::SECTOR_ADMIN->value && empty($this->region_id)) {
            $errors['region_id'] = 'Region tələb olunur';
        }

        if ($this->user_type === UserType::SCHOOL_ADMIN->value) {
            if (empty($this->region_id)) {
                $errors['region_id'] = 'Region tələb olunur';
            }
            if (empty($this->sector_id)) {
                $errors['sector_id'] = 'Sektor tələb olunur';
            }
            if (empty($this->school_id)) {
                $errors['school_id'] = 'Məktəb tələb olunur';
            }
        }

        return $errors;
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'utis_code' => $this->utis_code,
            'email' => $this->email,
            'username' => $this->username,
            'password' => $this->password,
            'user_type' => $this->user_type,
            'region_id' => $this->region_id,
            'sector_id' => $this->sector_id,
            'school_id' => $this->school_id,
        ];
    }
}