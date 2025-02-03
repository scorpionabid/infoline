<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Interfaces\IRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseRepository implements IRepository
{
    protected $model;
    protected $table;

    public function getById(int $id): ?Model
    {
        return $this->model::find($id);
    }

    public function getAll(array $criteria = []): Collection
    {
        $query = $this->model::query();
        
        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }
        
        return $query->get();
    }

    abstract public function create(array $data): Model;
    abstract public function update(int $id, array $data): Model;
    
    public function delete(int $id): bool
    {
        $model = $this->getById($id);
        return $model ? $model->delete() : false;
    }
}