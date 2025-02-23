<?php

namespace App\Services\School;

use App\Domain\Entities\School;
use App\Domain\Entities\User;
use App\Exceptions\SchoolException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use App\Events\School\AdminAssigned;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Cache;

class SchoolService
{
    /**
     * Get filtered schools.
     *
     * @param int|null $regionId
     * @param int|null $sectorId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getFilteredSchools(?int $regionId = null, ?int $sectorId = null)
    {
        try {
            $cacheKey = 'schools_filtered_' . ($regionId ?? 'all') . '_' . ($sectorId ?? 'all');

            return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($regionId, $sectorId) {
                $query = School::with([
                    'sector.region',
                    'admin',
                    'admins',
                    'categories',
                    'data'
                ]);

                if ($regionId) {
                    $query->whereHas('sector', function ($q) use ($regionId) {
                        $q->where('region_id', $regionId);
                    });
                }

                if ($sectorId) {
                    $query->where('sector_id', $sectorId);
                }

                Log::info('Schools filtered', [
                    'region_id' => $regionId,
                    'sector_id' => $sectorId,
                    'user_id' => auth()->id()
                ]);

                return $query;
            });
        } catch (\Exception $e) {
            Log::error('Error filtering schools', [
                'error' => $e->getMessage(),
                'region_id' => $regionId,
                'sector_id' => $sectorId
            ]);
            throw new SchoolException('Məktəbləri filtirləyərkən xəta baş verdi.');
        }
    }

    /**
     * Get available admins for assignment.
     *
     * @param string $search
     * @return Collection
     */
    public function getAvailableAdmins(?string $search = null): Collection
    {
        $cacheKey = 'available_admins' . ($search ? '_' . md5($search) : '');

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($search) {
            $schoolAdminRole = Role::where('name', 'school_admin')->firstOrFail();

            $query = User::role($schoolAdminRole)
                ->whereDoesntHave('schools')
                ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) as text"));

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%");
                });
            }

            return $query->get();
        });
    }

    /**
     * Assign admin to school.
     *
     * @param School $school
     * @param int $adminId
     * @throws \Exception
     */
    public function assignAdmin(School $school, int $adminId): void
    {
        try {
            DB::beginTransaction();

            $admin = User::findOrFail($adminId);
            $schoolAdminRole = Role::where('name', 'school_admin')->firstOrFail();

            if (!$admin->hasRole($schoolAdminRole)) {
                throw new SchoolException('Seçilmiş istifadəçi məktəb admini deyil.');
            }

            if ($admin->schools()->exists()) {
                throw new SchoolException('Seçilmiş admin artıq başqa məktəbə təyin edilib.');
            }

            $school->admins()->attach($admin->id);

            if (!$school->admin_id) {
                $school->admin_id = $admin->id;
                $school->save();
            }

            event(new AdminAssigned($school, $admin));

            Log::info('Admin assigned to school', [
                'school_id' => $school->id,
                'admin_id' => $admin->id,
                'user_id' => auth()->id()
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin assignment failed', [
                'error' => $e->getMessage(),
                'school_id' => $school->id,
                'admin_id' => $adminId
            ]);
            throw $e;
        }
    }
    /**
     * Yeni məktəb yaratmaq
     */
    public function create(array $data): School
    {
        try {
            DB::beginTransaction();

            // Get sector's region_id
            $sector = \App\Domain\Entities\Sector::findOrFail($data['sector_id']);
            
            $school = School::create([
                'name' => $data['name'],
                'utis_code' => $data['utis_code'],
                'type' => $data['type'],
                'sector_id' => $data['sector_id'],
                'region_id' => $sector->region_id, // Use sector's region_id
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'address' => $data['address'] ?? null,
                'status' => true
            ]);

            Log::info('School created', [
                'school_id' => $school->id,
                'user_id' => auth()->id()
            ]);

            DB::commit();
            return $school;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('School creation failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw new SchoolException('Məktəb yaradılarkən xəta baş verdi: ' . $e->getMessage());
        }
    }

    /**
     * Məktəb məlumatlarını yeniləmək
     */
    public function update(School $school, array $data): School
    {
        try {
            DB::beginTransaction();

            $school->update([
                'name' => $data['name'],
                'utis_code' => $data['utis_code'],
                'type' => $data['type'],
                'sector_id' => $data['sector_id'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'address' => $data['address'] ?? null
            ]);

            Log::info('School updated', [
                'school_id' => $school->id,
                'user_id' => auth()->id()
            ]);

            DB::commit();
            return $school->fresh();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('School update failed', [
                'error' => $e->getMessage(),
                'school_id' => $school->id,
                'data' => $data
            ]);
            throw new SchoolException('Məktəb yenilənərkən xəta baş verdi: ' . $e->getMessage());
        }
    }

    /**
     * Məktəbi silmək
     */
    public function delete(School $school): bool
    {
        try {
            if ($this->hasActiveDependencies($school)) {
                throw new SchoolException('Bu məktəbə aid aktiv məlumatlar var');
            }

            DB::beginTransaction();

            $school->delete();

            Log::info('School deleted', [
                'school_id' => $school->id,
                'user_id' => auth()->id()
            ]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('School deletion failed', [
                'error' => $e->getMessage(),
                'school_id' => $school->id
            ]);
            throw new SchoolException('Məktəb silinərkən xəta baş verdi: ' . $e->getMessage());
        }
    }



    /**
     * Məktəbin statusunu dəyişmək
     */
    public function updateStatus(School $school, bool $status): School
    {
        try {
            $school->update(['status' => $status]);

            Log::info('School status updated', [
                'school_id' => $school->id,
                'status' => $status,
                'user_id' => auth()->id()
            ]);

            return $school->fresh();

        } catch (\Exception $e) {
            Log::error('Status update failed', [
                'error' => $e->getMessage(),
                'school_id' => $school->id
            ]);
            throw new SchoolException('Status dəyişdirilməsi zamanı xəta baş verdi: ' . $e->getMessage());
        }
    }

    /**
     * Bulk əməliyyatlar
     */
    public function bulkAction(string $action, array $schoolIds): bool
    {
        try {
            DB::beginTransaction();

            $schools = School::whereIn('id', $schoolIds)->get();

            foreach ($schools as $school) {
                switch ($action) {
                    case 'activate':
                        $school->update(['status' => true]);
                        break;
                    case 'deactivate':
                        $school->update(['status' => false]);
                        break;
                    case 'delete':
                        if (!$this->hasActiveDependencies($school)) {
                            $school->delete();
                        }
                        break;
                }
            }

            Log::info('Bulk action completed', [
                'action' => $action,
                'school_ids' => $schoolIds,
                'user_id' => auth()->id()
            ]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk action failed', [
                'error' => $e->getMessage(),
                'action' => $action,
                'school_ids' => $schoolIds
            ]);
            throw new SchoolException('Bulk əməliyyat zamanı xəta baş verdi: ' . $e->getMessage());
        }
    }

    /**
     * Məktəbin asılılıqlarını yoxlamaq
     */
    private function hasActiveDependencies(School $school): bool
    {
        return $school->admins()->where('status', true)->exists() ||
               $school->data()->exists();
    }

}