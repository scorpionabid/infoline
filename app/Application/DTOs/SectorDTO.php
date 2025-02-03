<?php

namespace App\Application\DTOs;

use App\Domain\Entities\Region;

class SectorDTO
{
    public ?string $name = null;
    public ?string $phone = null;
    public ?int $region_id = null;

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
            $errors['name'] = 'Sektor adı tələb olunur';
        }

        if (!empty($this->phone) && !preg_match('/^\+?[0-9]{10,15}$/', $this->phone)) {
            $errors['phone'] = 'Düzgün telefon nömrəsi daxil edin';
        }

        if (empty($this->region_id)) {
            $errors['region_id'] = 'Region tələb olunur';
        } else {
            $region = Region::find($this->region_id);
            if (!$region) {
                $errors['region_id'] = 'Seçilmiş region mövcud deyil';
            }
        }

        return $errors;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'phone' => $this->phone,
            'region_id' => $this->region_id,
        ];
    }
}
