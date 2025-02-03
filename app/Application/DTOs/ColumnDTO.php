<?php
namespace App\Application\DTOs;

use Carbon\Carbon;

class ColumnDTO
{
    public ?string $name = null;
    public ?string $data_type = null;
    public ?string $end_date = null;
    public ?int $input_limit = null;
    public ?int $category_id = null;
    public ?array $choices = null;
    public bool $isNew = true;

    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function validate(bool $isDeactivation = false): array
    {
        $errors = [];

    // Name validation
        if (empty($this->name)) {
            $errors['name'] = 'Sütun adı tələb olunur';
        } elseif (strlen($this->name) > 255) {
            $errors['name'] = 'Sütun adı 255 simvoldan çox ola bilməz';
        }

    // Data type validation
        if (empty($this->data_type)) {
            $errors['data_type'] = 'Data tipi tələb olunur';
        } elseif (!in_array($this->data_type, ['text', 'number', 'date', 'select', 'multiselect', 'file'])) {
            $errors['data_type'] = 'Yanlış data tipi';
        }

    // End date validation - deaktivasiya zamanı keçmiş tarix validasiyasını etmirik
        if ($this->end_date !== null && !$isDeactivation) {
            try {
                $endDate = Carbon::parse($this->end_date);
                if ($endDate->isPast()) {
                    $errors['end_date'] = 'Bitmə tarixi keçdiğiniz tarixdən sonra olmalıdır';
                }
            } catch (\Exception $e) {
                $errors['end_date'] = 'Yanlış tarix formatı';
            }
        }

    // Input limit validation
        if ($this->input_limit !== null) {
            if (!is_numeric($this->input_limit) || $this->input_limit <= 0) {
                $errors['input_limit'] = 'Daxiletmə limiti müsbət ədəd olmalıdır';
            }
        }

    // Category validation
        if (empty($this->category_id)) {
            $errors['category_id'] = 'Kateqoriya tələb olunur';
        }

        return $errors;
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'data_type' => $this->data_type,
            'end_date' => $this->end_date,
            'input_limit' => $this->input_limit,
            'category_id' => $this->category_id,
            'choices' => $this->choices,
        ], fn($value) => $value !== null);
    }
}