<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Sector;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SectorRepository extends BaseRepository
{
    protected $model = Sector::class;

    public function getAll(array $criteria = []): Collection
    {
        $query = Sector::query();
        
        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }
        
        return $query->get();
    }

    public function getById(int $id): ?Sector
    {
        return Sector::find($id);
    }

    public function create(array $data): Model
    {
        return Sector::create($data);
    }

    public function update(int $id, array $data): Model
    {
        $sector = $this->getById($id);
        $sector->update($data);
        return $sector->fresh();
    }

    public function delete(int $id): bool
    {
        return Sector::find($id)->delete();
    }

    public function getByRegionId(int $regionId): Collection
    {
        return Sector::where('region_id', $regionId)->get();
    }

    public function getSchoolsCount(int $id): int
    {
        return Sector::find($id)->schools()->count();
    }

    public function getUsersCount(int $id): int
    {
        return Sector::find($id)->users()->count();
    }
}
