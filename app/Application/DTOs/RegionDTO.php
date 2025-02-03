<?php

namespace App\Application\DTOs;

class RegionDTO
{
    public ?string $name = null;
    public ?string $phone = null;

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

        if (empty($this->name)) {
            $errors['name'] = 'Region adı tələb olunur';
        }

        if (!empty($this->phone) && !preg_match('/^\+?[0-9]{10,15}$/', $this->phone)) {
            $errors['phone'] = 'Düzgün telefon nömrəsi daxil edin';
        }

        return $errors;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'phone' => $this->phone,
        ];
    }
}
