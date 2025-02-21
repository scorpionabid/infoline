<?php

namespace App\Application\Services;

use App\Application\DTOs\UserDTO;
use App\Infrastructure\Repositories\UserRepository;
use App\Domain\Entities\User;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class UserService
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    // Mövcud metodlar...

    public function createUser(UserDTO $dto): User 
    {
        $errors = $dto->validate();
        if (!empty($errors)) {
            throw new InvalidArgumentException(json_encode($errors));
        }

        if ($this->repository->findByUsername($dto->username)) {
            throw new InvalidArgumentException("Bu istifadəçi adı artıq mövcuddur");
        }

        return $this->repository->create($dto->toArray());
    }

    public function getUsersByConditions(array $conditions): Collection
    {
        return $this->repository->findWhere($conditions);
    }

    public function getUsersByType(string $type, array $additionalConditions = []): Collection
    {
        $conditions = array_merge(['type' => $type], $additionalConditions);
        return $this->getUsersByConditions($conditions);
    }

    public function updateUser(int $id, UserDTO $dto): User
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

        $userData = $dto->toArray();
        if (!$dto->password) {
            unset($userData['password']);
        }

        return $this->repository->update($id, $userData);
    }

    public function deactivateUser(int $id): User
    {
        $user = $this->repository->getById($id);
        if (!$user) {
            throw new InvalidArgumentException("İstifadəçi tapılmadı");
        }

        return $this->repository->update($id, ['is_active' => false]);
    }
}