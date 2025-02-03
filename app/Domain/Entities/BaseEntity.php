<?php

namespace App\Domain\Entities;

use App\Domain\Interfaces\IEntity;

abstract class BaseEntity implements IEntity
{
    protected ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    abstract public function toArray(): array;
}