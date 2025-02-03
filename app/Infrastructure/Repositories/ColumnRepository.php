<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Column;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ColumnRepository extends BaseRepository
{
    protected $model = Column::class;

    public function getAll(array $criteria = []): Collection
    {
        $query = Column::query();
        
        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }
        
        return $query->get();
    }

    public function getById(int $id): ?Column
    {
        return Column::find($id);
    }

    public function create(array $data): Model
    {
        return Column::create($data);
    }

    public function update(int $id, array $data): Model
    {
        $column = $this->getById($id);
        $column->update($data);
        return $column->fresh();
    }

    public function delete(int $id): bool
    {
        return Column::find($id)->delete();
    }

    public function getByCategoryId(int $categoryId): Collection
    {
        return Column::where('category_id', $categoryId)->get();
    }

    public function getActiveColumns(): Collection
    {
        return Column::whereNull('end_date')
            ->orWhere('end_date', '>', now())
            ->get();
    }

    public function getActiveByCategoryId(int $categoryId): Collection
    {
        return Column::where('category_id', $categoryId)
            ->whereNull('end_date')
            ->orWhere('end_date', '>', now())
            ->get();
    }

    public function getActiveByRegionId(int $regionId): Collection
    {
        return Column::whereHas('category', function ($query) use ($regionId) {
            $query->whereHas('region', function ($query) use ($regionId) {
                $query->where('id', $regionId);
            });
        })
        ->whereNull('end_date')
        ->orWhere('end_date', '>', now())
        ->get();
    }

    public function getWithChoices(int $id): ?Column
    {
        return Column::with('choices')->find($id);
    }
}
