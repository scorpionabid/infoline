<?php

namespace App\Application\DTOs;

abstract class BaseDTO
{
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    abstract public function toArray(): array;
    abstract public function validate(): array;
}