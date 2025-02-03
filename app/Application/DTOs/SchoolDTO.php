<?php

namespace App\Application\DTOs;

class SchoolDTO
{
    public ?string $name = null;
    public ?string $utis_code = null;
    public ?string $phone = null;
    public ?string $email = null;
    public ?int $sector_id = null;

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

        // Name validation
        if (empty($this->name)) {
            $errors['name'] = 'Məktəb adı tələb olunur';
        } elseif (strlen($this->name) > 255) {
            $errors['name'] = 'Məktəb adı 255 simvoldan çox ola bilməz';
        }

        // UTIS code validation
        if (empty($this->utis_code)) {
            $errors['utis_code'] = 'UTIS kodu tələb olunur';
        } elseif (!preg_match('/^\d{7}$/', $this->utis_code)) {
            $errors['utis_code'] = 'UTIS kodu 7 rəqəmdən ibarət olmalıdır';
        }

        // Phone validation
        if (!empty($this->phone) && !preg_match('/^\+?[0-9]{10,15}$/', $this->phone)) {
            $errors['phone'] = 'Düzgün telefon nömrəsi daxil edin';
        }

        // Email validation
        if (!empty($this->email) && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Düzgün email formatı daxil edin';
        }

        // Sector validation
        if (empty($this->sector_id)) {
            $errors['sector_id'] = 'Sektor tələb olunur';
        }

        return $errors;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'utis_code' => $this->utis_code,
            'phone' => $this->phone,
            'email' => $this->email,
            'sector_id' => $this->sector_id,
        ];
    }
}