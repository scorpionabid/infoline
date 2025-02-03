<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Region;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class RegionRepository extends BaseRepository
{
    protected $model = Region::class;
    protected $table = 'regions';

    public function getAll(array $criteria = []): Collection
    {
        $query = Region::query();
        
        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }
        
        return $query->get();
    }

    public function getById(int $id): ?Region
    {
        return Region::find($id);
    }

    public function create(array $data): Model
    {
        return Region::create($data);
    }

    public function update(int $id, array $data): Model
    {
        $region = $this->getById($id);
        $region->update($data);
        return $region->fresh();
    }

    public function delete(int $id): bool
    {
        $region = $this->getById($id);
        if (!$region) {
            return false;
        }
        return $region->delete();
    }

    public function getSectorsCount(int $id): int
    {
        return Region::find($id)->sectors()->count();
    }

    public function getSchoolsCount(int $id): int
    {
        return Region::find($id)->schools()->count();
    }
}
