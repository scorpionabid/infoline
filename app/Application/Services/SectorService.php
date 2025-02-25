<?php

namespace App\Application\Services;

use App\Application\DTOs\SectorDTO;
use App\Application\DTOs\UserDTO;
use App\Domain\Entities\Permission;
use App\Domain\Entities\Role;
use App\Domain\Entities\Sector;
use App\Domain\Entities\User;
use App\Domain\Enums\UserType;
use App\Exceptions\SectorAdminUpdateException;
use App\Infrastructure\Repositories\SectorRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

   /**
    * Sektor adminini deaktiv et və rollarını sil
    * 
    * @param int $sectorId
    * @param UserDTO $userDTO
    * @return Sector
    */
   /**
    * Sektor üçün yeni admin təyin et və ya mövcud admini yenilə
    *
    * @param int $sectorId Sektor ID
    * @param UserDTO $userDTO İstifadəçi məlumatları
    * @return array Sektor və admin məlumatları
    * @throws SectorAdminUpdateException
    * @throws InvalidArgumentException
    */
   public function updateSectorAdmin(int $sectorId, UserDTO $userDTO): array
   {
       // Request validation
       $validationErrors = $userDTO->validate();
       if (!empty($validationErrors)) {
           Log::warning('Sektor admin DTO validasiya xətası', [
               'sector_id' => $sectorId,
               'errors' => $validationErrors
           ]);
           throw new InvalidArgumentException('Validasiya xətası: ' . json_encode($validationErrors));
       }

       DB::beginTransaction();
       try {
           Log::info('Sektor admin yeniləməsi başladı', [
               'sector_id' => $sectorId,
               'user_data' => array_diff_key($userDTO->toArray(), ['password' => ''])
           ]);

           // Sektoru yoxla
           $sector = $this->getById($sectorId);
           if (!$sector) {
               throw new InvalidArgumentException("Sektor tapılmadı: {$sectorId}");
           }

           // Köhnə admini yoxla və deaktiv et
           if ($sector->admin_id) {
               $this->deactivateOldAdmin($sector->admin_id);
           }

           // Yeni admin yarat
           $admin = $this->createNewAdmin($userDTO, $sector);

           // Sektoru yenilə
           $updatedSector = $this->updateSectorWithNewAdmin($sector, $admin);

           // Commit və log
           DB::commit();
           Log::info('Sektor admin yeniləməsi uğurla tamamlandı', [
               'sector_id' => $sectorId,
               'admin_id' => $admin->id,
               'sector' => $updatedSector->toArray()
           ]);

           return [
               'success' => true,
               'sector' => $updatedSector,
               'admin' => $admin,
               'message' => 'Sektor admini uğurla təyin edildi'
           ];

       } catch (\Exception $e) {
           DB::rollBack();
           $this->logError('Sektor admin yeniləməsi xətası', $e, [
               'sector_id' => $sectorId,
               'user_dto' => $userDTO->toArray()
           ]);
           throw new SectorAdminUpdateException($e->getMessage());
       }
   }

   /**
    * Köhnə admini deaktiv et
    */
   private function deactivateOldAdmin(int $adminId): void
   {
       try {
           $oldAdmin = $this->userService->getById($adminId);
           if ($oldAdmin) {
               Log::info('Köhnə admin deaktiv edilir', ['admin_id' => $adminId]);
               $oldAdmin->deactivate();
               $oldAdmin->roles()->detach();
               Log::info('Köhnə admin deaktiv edildi', ['admin_id' => $adminId]);
           }
       } catch (\Exception $e) {
           Log::warning('Köhnə admin deaktiv edilərkən xəta', [
               'admin_id' => $adminId,
               'error' => $e->getMessage()
           ]);
           throw $e;
       }
   }

   /**
    * Yeni admin yarat
    */
   private function createNewAdmin(UserDTO $userDTO, Sector $sector): User
   {
       try {
           Log::info('Yeni admin yaradılır', [
               'sector_id' => $sector->id,
               'user_type' => $userDTO->user_type
           ]);

           $admin = $this->userService->createUser($userDTO);
           $this->assignAdminRoles($admin, $sector);

           Log::info('Yeni admin yaradıldı', [
               'admin_id' => $admin->id,
               'sector_id' => $sector->id
           ]);

           return $admin;
       } catch (\Exception $e) {
           Log::error('Yeni admin yaradılarkən xəta', [
               'sector_id' => $sector->id,
               'error' => $e->getMessage()
           ]);
           throw $e;
       }
   }

   /**
    * Sektoru yeni adminlə yenilə
    */
   private function updateSectorWithNewAdmin(Sector $sector, User $admin): Sector
   {
       try {
           $updateData = ['admin_id' => $admin->id];
           $updatedSector = $this->repository->update($sector->id, $updateData);
           
           // Admin history
           $this->createAdminHistory($updatedSector, $admin);

           Log::info('Sektor admin ilə yeniləndi', [
               'sector_id' => $sector->id,
               'admin_id' => $admin->id
           ]);

           return $updatedSector;
       } catch (\Exception $e) {
           Log::error('Sektor yenilənərkən xəta', [
               'sector_id' => $sector->id,
               'admin_id' => $admin->id,
               'error' => $e->getMessage()
           ]);
           throw $e;
       }
   }

   /**
    * Xətaları loqla
    */
   private function logError(string $message, \Exception $e, array $context = []): void
   {
       $errorContext = array_merge($context, [
           'error' => $e->getMessage(),
           'trace' => $e->getTraceAsString(),
           'file' => $e->getFile(),
           'line' => $e->getLine()
       ]);

       Log::error($message, $errorContext);
   }

   private function assignAdminRoles(User $admin, Sector $sector): void 
   {
       // Sektor admin rolunu təyin et
       $sectorAdminRole = Role::where('name', 'sector-admin')->first();
       $admin->roles()->sync([$sectorAdminRole->id]);

       // Sektor adminləri üçün icazələri əlavə et
       $permissions = Permission::where('group', 'sector')->pluck('id');
       $admin->permissions()->sync($permissions);

       // Sektor-admin əlaqəsini yarat
       DB::table('sector_admin_roles')->insert([
           'user_id' => $admin->id,
           'sector_id' => $sector->id,
           'admin_type' => 'primary',
           'created_at' => now()
       ]);
   }

   private function removeAdminRoles(User $admin, Sector $sector): void 
   {
       $admin->roles()->detach();
       $admin->permissions()->detach();
       
       DB::table('sector_admin_roles')
           ->where('user_id', $admin->id)
           ->where('sector_id', $sector->id)
           ->delete();
   }

   private function createAdminHistory(Sector $sector, User $admin): void 
   {
       DB::table('sector_admin_history')->insert([
           'sector_id' => $sector->id,
           'user_id' => $admin->id,
           'assigned_at' => now(),
           'assigned_by' => auth()->id()
       ]);
   }

   public function cleanupSoftDeleted(): int
   {
       try {
           return DB::transaction(function () {
               $deletedSectors = Sector::onlyTrashed()->get();
               $count = 0;

               foreach ($deletedSectors as $sector) {
                   if ($sector->schools()->count() === 0) {
                       $sector->forceDelete();
                       $count++;
                   }
               }

               return $count;
           });
       } catch (\Exception $e) {
           Log::error('Error cleaning up soft deleted sectors', [
               'error' => $e->getMessage(),
               'trace' => $e->getTraceAsString()
           ]);
           throw $e;
       }
   }
}