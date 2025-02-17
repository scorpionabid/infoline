<?php

namespace App\Application\Services;

use App\Application\DTOs\SectorDTO;
use App\Application\DTOs\UserDTO;
use App\Domain\Entities\Sector;
use App\Domain\Enums\UserType;
use App\Infrastructure\Repositories\SectorRepository;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SectorService
{
   private SectorRepository $repository;
   private UserService $userService;

   public function __construct(
       SectorRepository $repository,
       UserService $userService
   ) {
       $this->repository = $repository;
       $this->userService = $userService;
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
       if ($this->repository->getSchoolsCount($id) > 0) {
           throw new InvalidArgumentException('Bu sektora aid məktəblər var. Əvvəlcə məktəbləri silin.');
       }

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

   public function createSectorWithAdmin(SectorDTO $sectorDTO, UserDTO $userDTO): Sector
   {
       DB::beginTransaction();
       try {
           $sector = $this->create($sectorDTO);
           
           $userDTO->type = UserType::SECTOR_ADMIN->value;
           $userDTO->sector_id = $sector->id;
           $admin = $this->userService->createUser($userDTO);

           DB::commit();
           return $sector;
       } catch (\Exception $e) {
           DB::rollBack();
           throw $e;
       }
   }

   public function getSectorAdmins(?int $regionId = null): array
   {
       $query = ['type' => UserType::SECTOR_ADMIN->value];
       if ($regionId) {
           $query['sectors.region_id'] = $regionId;
       }
       return $this->userService->getUsersByConditions($query)->toArray();
   }

   public function updateSectorAdmin(int $sectorId, UserDTO $userDTO): Sector
   {
       DB::beginTransaction();
       try {
           $sector = $this->getById($sectorId);
           if (!$sector) {
               throw new InvalidArgumentException('Sektor tapılmadı');
           }

           if ($sector->admin_id) {
               $this->userService->updateUser($sector->admin_id, $userDTO);
           } else {
               $userDTO->type = UserType::SECTOR_ADMIN->value;
               $userDTO->sector_id = $sectorId;
               $admin = $this->userService->createUser($userDTO);
               
               $sector->admin_id = $admin->id;
               $this->repository->update($sectorId, ['admin_id' => $admin->id]);
           }

           DB::commit();
           return $sector;
       } catch (\Exception $e) {
           DB::rollBack();
           throw $e;
       }
   }
}