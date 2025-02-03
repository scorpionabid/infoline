<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CategoryRepository extends BaseRepository
{
    protected $model = Category::class;

    public function getAll(array $criteria = []): Collection
    {
        $query = Category::query();
        
        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }
        
        return $query->get();
    }

    public function getById(int $id): ?Category
    {
        return Category::find($id);
    }

    public function create(array $data): Model
    {
        return Category::create($data);
    }

    public function update(int $id, array $data): Model
    {
        $category = $this->getById($id);
        $category->update($data);
        return $category->fresh();
    }

    public function delete(int $id): bool
    {
        return Category::find($id)->delete();
    }

    public function getWithActiveColumns(): Collection
    {
        return Category::with(['columns' => function ($query) {
            $query->whereNull('end_date')
                ->orWhere('end_date', '>', now());
        }])->get();
    }

    public function getColumnsCount(int $id): int
    {
        return Category::find($id)->columns()->count();
    }

    public function getActiveColumnsCount(int $id): int
    {
        return Category::find($id)->columns()
            ->whereNull('end_date')
            ->orWhere('end_date', '>', now())
            ->count();
    }
}