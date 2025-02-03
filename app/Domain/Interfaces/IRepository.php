<?php

namespace App\Domain\Interfaces;

interface IRepository
{
    public function getById(int $id);
    public function getAll(array $criteria = []);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id): bool;
}