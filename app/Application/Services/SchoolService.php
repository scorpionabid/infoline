<?php

namespace App\Application\Services;

use App\Application\DTOs\SchoolDTO;
use App\Infrastructure\Repositories\SchoolRepository;
use App\Domain\Entities\School;
use InvalidArgumentException;

class SchoolService
{
    private SchoolRepository $repository;

    public function __construct(SchoolRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(SchoolDTO $dto): School
    {
        $errors = $dto->validate();
        if (!empty($errors)) {
            throw new InvalidArgumentException(json_encode($errors));
        }

        // UTIS kodunun unikallığını yoxla
        if ($this->repository->findByUtisCode($dto->utis_code)) {
            throw new InvalidArgumentException('Bu UTIS kodu artıq mövcuddur');
        }

        return $this->repository->create($dto->toArray());
    }

    public function update(int $id, SchoolDTO $dto): School
    {
        $errors = $dto->validate();
        if (!empty($errors)) {
            throw new InvalidArgumentException(json_encode($errors));
        }

        $existingSchool = $this->repository->getById($id);
        if (!$existingSchool) {
            throw new InvalidArgumentException('Məktəb tapılmadı');
        }

        // UTIS kodunun unikallığını yoxla (əgər dəyişdirilibsə)
        if ($dto->utis_code !== $existingSchool->utis_code && 
            $this->repository->findByUtisCode($dto->utis_code)) {
            throw new InvalidArgumentException('Bu UTIS kodu artıq mövcuddur');
        }

        return $this->repository->update($id, $dto->toArray());
    }

    public function delete(int $id): bool
    {
        $school = $this->repository->getById($id);
        if (!$school) {
            throw new InvalidArgumentException('Məktəb tapılmadı');
        }

        // Məktəbin administratorları varsa silməyə icazə vermə
        if ($this->repository->getAdminsCount($id) > 0) {
            throw new InvalidArgumentException('Bu məktəbə aid administratorlar var. Əvvəlcə administratorları silin və ya başqa məktəbə köçürün.');
        }

        return $this->repository->delete($id);
    }

    public function getAll(): array
    {
        return $this->repository->getAll()->toArray();
    }

    public function getById(int $id): ?School
    {
        return $this->repository->getById($id);
    }

    public function getBySectorId(int $sectorId): array
    {
        return $this->repository->getBySectorId($sectorId)->toArray();
    }

    public function getWithFullDetails(int $id): ?School
    {
        return $this->repository->getWithFullDetails($id);
    }

    public function getAdminsCount(int $id): int
    {
        return $this->repository->getAdminsCount($id);
    }
    public function getSchoolAdmins(int $id): array
    {
        $school = $this->repository->getById($id);
    if (!$school) {
        throw new InvalidArgumentException('Məktəb tapılmadı');
    }
    
    return $school->admins()->get()->toArray();
    }
}