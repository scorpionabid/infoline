<?php

namespace App\Application\Services;

use App\Application\DTOs\SectorDTO;
use App\Domain\Entities\Sector;
use App\Infrastructure\Repositories\SectorRepository;
use InvalidArgumentException;

class SectorService
{
    private SectorRepository $repository;

    public function __construct(SectorRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(SectorDTO $dto): Sector
    {
        $errors = $dto->validate();
        if (!empty($errors)) {
            throw new InvalidArgumentException(json_encode($errors));
        }

        return $this->repository->create($dto->toArray());
    }

    public function update(int $id, SectorDTO $dto): Sector
    {
        $errors = $dto->validate();
        if (!empty($errors)) {
            throw new InvalidArgumentException(json_encode($errors));
        }

        return $this->repository->update($id, $dto->toArray());
    }

    public function delete(int $id): bool
    {
        // Sektorun məktəbləri varsa silməyə icazə vermə
        if ($this->repository->getSchoolsCount($id) > 0) {
            throw new InvalidArgumentException('Bu sektora aid məktəblər var. Əvvəlcə məktəbləri silin.');
        }

        // Sektorun istifadəçiləri varsa silməyə icazə vermə
        if ($this->repository->getUsersCount($id) > 0) {
            throw new InvalidArgumentException('Bu sektora aid istifadəçilər var. Əvvəlcə istifadəçiləri silin və ya başqa sektora köçürün.');
        }

        return $this->repository->delete($id);
    }

    public function getAll(): array
    {
        return $this->repository->getAll()->toArray();
    }

    public function getById(int $id): ?Sector
    {
        return $this->repository->getById($id);
    }

    public function getByRegionId(int $regionId): array
    {
        return $this->repository->getByRegionId($regionId)->toArray();
    }

    public function getSchoolsCount(int $id): int
    {
        return $this->repository->getSchoolsCount($id);
    }

    public function getUsersCount(int $id): int
    {
        return $this->repository->getUsersCount($id);
    }
}
