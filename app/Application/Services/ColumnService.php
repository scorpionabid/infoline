<?php

namespace App\Application\Services;

use App\Application\DTOs\ColumnDTO;
use App\Infrastructure\Repositories\ColumnRepository;
use App\Domain\Entities\Column;
use InvalidArgumentException;

class ColumnService
{
   private ColumnRepository $repository;

   public function __construct(ColumnRepository $repository)
   {
       $this->repository = $repository;
   }

   public function create(ColumnDTO $dto): Column
   {
       // 1. Normal yaradılma validasiyası
       $errors = $dto->validate();
       if (!empty($errors)) {
           throw new InvalidArgumentException(json_encode($errors));
       }

       // 2. Seçim tipləri üçün choices yoxlaması
       if (in_array($dto->data_type, ['select', 'multiselect'])) {
           $choices = $dto->choices ?? [];
           if (empty($choices)) {
               throw new InvalidArgumentException('Seçim tipli sütun üçün variantlar tələb olunur');
           }
       }

       // 3. Sütunu yaradaq
       return $this->repository->create($dto->toArray());
   }

   public function deactivate(Column $column, string $endDate): Column
   {
       // Deaktivasiya üçün DTO yaradaq
       $dto = new ColumnDTO([
           'end_date' => $endDate, 
           'name' => $column->name,
           'data_type' => $column->data_type,
           'category_id' => $column->category_id
       ]);
       
       // Deaktivasiya validasiyası
       $errors = $dto->validate(true);
       if (!empty($errors)) {
           throw new InvalidArgumentException(json_encode($errors));
       }

       return $this->repository->update($column->id, ['end_date' => $endDate]);
   }

   public function update(int $id, ColumnDTO $dto): Column
   {
       // 1. Normal update validasiyası
       $errors = $dto->validate();
       if (!empty($errors)) {
           throw new InvalidArgumentException(json_encode($errors));
       }

       // 2. Sütunu yoxlayaq
       $column = $this->repository->getById($id);
       if (!$column) {
           throw new InvalidArgumentException('Sütun tapılmadı');
       }

       // 3. Data tipini dəyişməyə icazə vermə
       if ($dto->data_type !== $column->data_type) {
           throw new InvalidArgumentException('Sütunun data tipini dəyişmək mümkün deyil');
       }

       return $this->repository->update($id, $dto->toArray());
   }

   public function delete(int $id): bool
   {
       $column = $this->repository->getById($id);
       if (!$column) {
           throw new InvalidArgumentException('Sütun tapılmadı');
       }

       return $this->repository->delete($id);
   }

   public function getAll(): array
   {
       return $this->repository->getAll()->toArray();
   }

   public function getById(int $id): ?Column
   {
       return $this->repository->getById($id);
   }

   public function getByCategoryId(int $categoryId): array
   {
       return $this->repository->getByCategoryId($categoryId)->toArray();
   }

   public function getActiveColumns(): array
   {
       return $this->repository->getActiveColumns()->toArray();
   }

   public function getActiveByCategoryId(int $categoryId): array
   {
       return $this->repository->getActiveByCategoryId($categoryId)->toArray();
   }

   public function getWithChoices(int $id): ?Column
   {
       return $this->repository->getWithChoices($id);
   }
}