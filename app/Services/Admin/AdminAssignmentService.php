<?php

namespace App\Services\Admin;

use App\Domain\Entities\{School, User};
use App\Domain\Enums\UserType;
use App\Events\School\AdminAssigned;
use App\Exceptions\AdminAssignmentException;
use Illuminate\Support\Facades\{DB, Log};

class AdminAssignmentService
{
    /**
     * Assign an admin to a school
     *
     * @param School $school
     * @param int $adminId
     * @return User
     * @throws AdminAssignmentException
     */
    public function assignToSchool(School $school, int $adminId): User
    {
        try {
            DB::beginTransaction();

            $admin = User::findOrFail($adminId);

            if ($admin->type !== UserType::SchoolAdmin) {
                throw new AdminAssignmentException('İstifadəçi məktəb admini deyil');
            }

            if ($admin->schools()->where('id', '!=', $school->id)->exists()) {
                throw new AdminAssignmentException('Bu admin artıq başqa məktəbə təyin edilib');
            }

            if ($school->admin_id === $admin->id) {
                throw new AdminAssignmentException('Bu admin artıq bu məktəbə təyin edilib');
            }

            $oldAdminId = $school->admin_id;
            $school->admin()->associate($admin);
            $school->save();

            // Fire event
            event(new AdminAssigned($school, $admin, $oldAdminId));

            DB::commit();

            return $admin->load('schools');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($e instanceof AdminAssignmentException) {
                throw $e;
            }

            Log::error('Failed to assign admin to school', [
                'school_id' => $school->id,
                'admin_id' => $adminId,
                'error' => $e->getMessage()
            ]);

            throw new AdminAssignmentException('Admin təyin edilərkən xəta baş verdi');
        }
    }

    /**
     * Remove admin from school
     *
     * @param School $school
     * @return void
     * @throws AdminAssignmentException
     */
    public function removeFromSchool(School $school): void
    {
        try {
            DB::beginTransaction();

            if (!$school->admin_id) {
                throw new AdminAssignmentException('Məktəbə admin təyin edilməyib');
            }

            $oldAdminId = $school->admin_id;
            $school->admin()->dissociate();
            $school->save();

            // Fire event
            event(new AdminAssigned($school, null, $oldAdminId));

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($e instanceof AdminAssignmentException) {
                throw $e;
            }

            Log::error('Failed to remove admin from school', [
                'school_id' => $school->id,
                'error' => $e->getMessage()
            ]);

            throw new AdminAssignmentException('Admin silinərkən xəta baş verdi');
        }
    }

    /**
     * Update school admin
     *
     * @param School $school
     * @param int|null $newAdminId
     * @return void
     * @throws AdminAssignmentException
     */
    public function updateSchoolAdmin(School $school, ?int $newAdminId): void
    {
        if (!$newAdminId) {
            $this->removeFromSchool($school);
            return;
        }

        $this->assignToSchool($school, $newAdminId);
    }
}
