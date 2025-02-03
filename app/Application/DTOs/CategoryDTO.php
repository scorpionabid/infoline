<?php

namespace App\Application\DTOs;

class CategoryDTO
{
    public ?string $name = null;

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
            $errors['name'] = 'Kateqoriya adı tələb olunur';
        } elseif (strlen($this->name) > 255) {
            $errors['name'] = 'Kateqoriya adı 255 simvoldan çox ola bilməz';
        }

        return $errors;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}