<?php

namespace App\Domain\Interfaces;

interface IEntity
{
    public function getId(): ?int;
    public function toArray(): array;
}