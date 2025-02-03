<?php

namespace App\Application\Services;

use App\Application\DTOs\UserDTO;
use App\Infrastructure\Repositories\UserRepository;
use App\Domain\Entities\User;
use InvalidArgumentException;

class UserService
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(UserDTO $dto): User
    {
        $errors = $dto->validate();
        if (!empty($errors)) {
            throw new InvalidArgumentException(json_encode($errors));
        }

        if ($this->repository->findByUsername($dto->username)) {
            throw new InvalidArgumentException("Bu istifadəçi adı artıq mövcuddur");
        }

        if ($this->repository->findByEmail($dto->email)) {
            throw new InvalidArgumentException("Bu email artıq mövcuddur");
        }

        if ($this->repository->findByUtisCode($dto->utis_code)) {
            throw new InvalidArgumentException("Bu UTIS kodu artıq mövcuddur");
        }

        return $this->repository->create($dto->toArray());
    }

    public function update(int $id, UserDTO $dto): User
    {
        $errors = $dto->validate();
        if (!empty($errors)) {
            throw new InvalidArgumentException(json_encode($errors));
        }

        $existingUser = $this->repository->getById($id);
        if (!$existingUser) {
            throw new InvalidArgumentException("İstifadəçi tapılmadı");
        }

        $userWithSameUsername = $this->repository->findByUsername($dto->username);
        if ($userWithSameUsername && $userWithSameUsername->id !== $id) {
            throw new InvalidArgumentException("Bu istifadəçi adı artıq mövcuddur");
        }

        $userWithSameEmail = $this->repository->findByEmail($dto->email);
        if ($userWithSameEmail && $userWithSameEmail->id !== $id) {
            throw new InvalidArgumentException("Bu email artıq mövcuddur");
        }

        $userWithSameUtisCode = $this->repository->findByUtisCode($dto->utis_code);
        if ($userWithSameUtisCode && $userWithSameUtisCode->id !== $id) {
            throw new InvalidArgumentException("Bu UTIS kodu artıq mövcuddur");
        }

        $userData = $dto->toArray();
        if (!$dto->password) {
            unset($userData['password']);
        }

        return $this->repository->update($id, $userData);
    }

    public function delete(int $id): bool
    {
        $user = $this->repository->getById($id);
        if (!$user) {
            throw new InvalidArgumentException("İstifadəçi tapılmadı");
        }

        return $this->repository->softDelete($id);
    }

    public function getById(int $id): ?User
    {
        return $this->repository->getById($id);
    }

    public function getBySectorId(int $sectorId): array
    {
        return $this->repository->getBySectorId($sectorId);
    }

    public function getBySchoolId(int $schoolId): array
    {
        return $this->repository->getBySchoolId($schoolId);
    }
}