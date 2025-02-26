<?php

use Illuminate\Database\Migrations\Migration;
use App\Domain\Entities\User;
use App\Domain\Entities\School;
use App\Domain\Enums\UserType;

class SyncSchoolAdmins extends Migration
{
    public function up()
    {
        // Find all school admin users
        $schoolAdmins = User::where('user_type', UserType::SCHOOL_ADMIN)
            ->whereNotNull('school_id')
            ->get();

        $count = 0;
        foreach ($schoolAdmins as $admin) {
            $school = School::find($admin->school_id);
            if ($school) {
                // Update school's admin_id
                $school->admin_id = $admin->id;
                $school->save();
                $count++;
            }
        }

        \Log::info("Synced $count school admins");
    }

    public function down()
    {
        // This migration is non-reversible
    }
}