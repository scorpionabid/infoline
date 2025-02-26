<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Entities\User;
use App\Domain\Entities\School;
use App\Domain\Enums\UserType;
use Illuminate\Support\Facades\DB;

class SyncSchoolAdmins extends Command
{
    protected $signature = 'schools:sync-admins';
    protected $description = 'Synchronize school admins with schools';

    public function handle()
    {
        $this->info('Starting school-admin synchronization...');
        
        try {
            DB::beginTransaction();
            
            // 1. Find users with school_id but no corresponding admin_id in schools
            $usersWithSchools = User::where('user_type', UserType::SCHOOL_ADMIN)
                ->whereNotNull('school_id')
                ->get();
                
            $this->info("Found {$usersWithSchools->count()} users with school_id to sync");
            
            $syncedFromUsers = 0;
            foreach ($usersWithSchools as $user) {
                $school = School::find($user->school_id);
                if ($school && !$school->admin_id) {
                    $school->admin_id = $user->id;
                    $school->save();
                    $syncedFromUsers++;
                    $this->line("Set admin_id {$user->id} for school {$school->id} ({$school->name})");
                }
            }
            
            // 2. Find schools with admin_id but users without corresponding school_id
            $schoolsWithAdmins = School::whereNotNull('admin_id')->get();
            
            $this->info("Found {$schoolsWithAdmins->count()} schools with admin_id to check");
            
            $syncedFromSchools = 0;
            foreach ($schoolsWithAdmins as $school) {
                $admin = User::find($school->admin_id);
                if ($admin && (!$admin->school_id || $admin->school_id != $school->id)) {
                    $admin->school_id = $school->id;
                    $admin->save();
                    $syncedFromSchools++;
                    $this->line("Set school_id {$school->id} for user {$admin->id} ({$admin->full_name})");
                }
            }
            
            DB::commit();
            
            $this->info("Synchronization completed:");
            $this->info("- {$syncedFromUsers} schools updated with admin_id from users");
            $this->info("- {$syncedFromSchools} users updated with school_id from schools");
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->error("Error during synchronization: {$e->getMessage()}");
            
            $this->error("Stack trace:");
            $this->error($e->getTraceAsString());
        }
    }
}