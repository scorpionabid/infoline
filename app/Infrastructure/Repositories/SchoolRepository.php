<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\School;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SchoolRepository extends BaseRepository
{
    protected $model = School::class;

    public function getAll(array $criteria = []): Collection
    {
        $query = School::query();
        
        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }
        
        return $query->get();
    }

    public function getById(int $id): ?School
    {
        return School::find($id);
    }

    public function create(array $data): Model
    {
        return School::create($data);
    }

    public function update(int $id, array $data): Model
    {
        $school = $this->getById($id);
        $school->update($data);
        return $school->fresh();
    }

    public function delete(int $id): bool
    {
        $model = $this->getById($id);
        if (!$model) {
            return false;
        }
        return $model->delete();
    }

    public function getBySectorId(int $sectorId): Collection
    {
        return School::where('sector_id', $sectorId)->get();
    }

    public function findByUtisCode(string $utisCode): ?School
    {
        return School::where('utis_code', $utisCode)->first();
    }

    public function getAdminsCount(int $id): int
    {
        return School::find($id)->admins()->count();
    }

    public function getByRegionId(int $regionId): Collection
    {
        return School::whereHas('sector', function ($query) use ($regionId) {
            $query->where('region_id', $regionId);
        })->get();
    }
}