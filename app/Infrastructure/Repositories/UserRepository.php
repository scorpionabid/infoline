<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\User;
use App\Domain\Enums\UserType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository
{
    public function findByUsername(string $username): ?User
    {
        return User::where('username', $username)->first();
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findByUtisCode(string $utisCode): ?User
    {
        return User::where('utis_code', $utisCode)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $id, array $data): User
    {
        $user = $this->getById($id);
        $user->update($data);
        return $user->fresh();
    }

    public function getById(int $id): ?User
    {
        return User::find($id);
    }

    public function softDelete(int $id): bool
    {
        return User::find($id)->delete();
    }

    public function getBySectorId(int $sectorId): Collection
    {
        return User::where('sector_id', $sectorId)
            ->where('user_type', UserType::SCHOOL_ADMIN->value)
            ->get();
    }

    public function getBySchoolId(int $schoolId): Collection
    {
        return User::where('school_id', $schoolId)->get();
    }

    public function getSectorAdmins(): Collection
    {
        return User::where('user_type', UserType::SECTOR_ADMIN->value)->get();
    }

    public function getSchoolAdmins(): Collection
    {
        return User::where('user_type', UserType::SCHOOL_ADMIN->value)->get();
    }
}