<?php

namespace App\Application\Services;

use App\Application\DTOs\CategoryDTO;
use App\Infrastructure\Repositories\CategoryRepository;
use App\Domain\Entities\Category;
use InvalidArgumentException;

class CategoryService
{
    private CategoryRepository $repository;

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(CategoryDTO $dto): Category
    {
        $errors = $dto->validate();
        if (!empty($errors)) {
            throw new InvalidArgumentException(json_encode($errors));
        }

        return $this->repository->create($dto->toArray());
    }

    public function update(int $id, CategoryDTO $dto): Category
    {
        $errors = $dto->validate();
        if (!empty($errors)) {
            throw new InvalidArgumentException(json_encode($errors));
        }

        $category = $this->repository->getById($id);
        if (!$category) {
            throw new InvalidArgumentException('Kateqoriya tapılmadı');
        }

        return $this->repository->update($id, $dto->toArray());
    }

    public function delete(int $id): bool
    {
        $category = $this->repository->getById($id);
        if (!$category) {
            throw new InvalidArgumentException('Kateqoriya tapılmadı');
        }

        // Kateqoriyanın aktiv sütunları varsa silməyə icazə vermə
        if ($this->repository->getActiveColumnsCount($id) > 0) {
            throw new InvalidArgumentException('Bu kateqoriyaya aid aktiv sütunlar var. Əvvəlcə sütunları silin və ya deaktiv edin.');
        }

        return $this->repository->delete($id);
    }

    public function getAll(): array
    {
        return $this->repository->getAll()->toArray();
    }

    public function getById(int $id): ?Category
    {
        return $this->repository->getById($id);
    }

    public function getWithActiveColumns(): array
    {
        return $this->repository->getWithActiveColumns()->toArray();
    }

    public function getColumnsCount(int $id): int
    {
        return $this->repository->getColumnsCount($id);
    }

    public function getActiveColumnsCount(int $id): int
    {
        return $this->repository->getActiveColumnsCount($id);
    }
}