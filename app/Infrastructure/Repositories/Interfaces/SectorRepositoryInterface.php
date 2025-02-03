<?php

namespace App\Infrastructure\Repositories\Interfaces;

use App\Domain\Entities\Sector;
use Illuminate\Database\Eloquent\Collection;

interface SectorRepositoryInterface
{
    public function getAll(): Collection;
    public function getById(int $id): ?Sector;
    public function getByRegionId(int $regionId): Collection;
    public function create(array $data): Sector;
    public function update(int $id, array $data): Sector;
    public function delete(int $id): bool;
    public function getSchoolsCount(int $id): int;
    public function getUsersCount(int $id): int;
}