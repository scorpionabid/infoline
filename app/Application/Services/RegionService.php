<?php

namespace App\Application\Services;

use App\Application\DTOs\RegionDTO;
use App\Domain\Entities\Region;
use App\Infrastructure\Repositories\RegionRepository;
use InvalidArgumentException;

class RegionService
{
    private RegionRepository $repository;

    public function __construct(RegionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(RegionDTO $dto): Region
    {
        $errors = $dto->validate();
        if (!empty($errors)) {
            throw new InvalidArgumentException(json_encode($errors));
        }

        return $this->repository->create($dto->toArray());
    }

    public function update(int $id, RegionDTO $dto): Region
    {
        $errors = $dto->validate();
        if (!empty($errors)) {
            throw new InvalidArgumentException(json_encode($errors));
        }

        return $this->repository->update($id, $dto->toArray());
    }

    public function delete(int $id): bool
    {
        // Mövcudluq yoxlanışı
        $region = $this->repository->getById($id);
        if (!$region) {
            throw new InvalidArgumentException('Region not found');
        }
    
        // Asılılıq yoxlanışı
        if ($this->repository->getSectorsCount($id) > 0) {
            throw new InvalidArgumentException('Bu regiona aid sektorlar var. Əvvəlcə sektorları silin.');
        }
    
        // Silmə əməliyyatı
        return $this->repository->delete($id);
    }

    public function getAll(): array
    {
        return $this->repository->getAll()->toArray();
    }

    public function getById(int $id): ?Region
    {
        return $this->repository->getById($id);
    }

    public function getSectorsCount(int $id): int
    {
        return $this->repository->getSectorsCount($id);
    }

    public function getSchoolsCount(int $id): int
    {
        return $this->repository->getSchoolsCount($id);
    }
}
