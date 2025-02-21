<?php

namespace App\Services;

use App\Models\{Sector, User};
use Illuminate\Support\Str;
use App\Mail\AdminCredentials;
use Mail;

class SectorService
{
    public function create(array $data)
    {
        return Sector::create($data);
    }

    public function update(int $id, array $data)
    {
        $sector = Sector::findOrFail($id);
        $sector->update($data);
        return $sector;
    }

    public function delete(int $id)
    {
        $sector = Sector::findOrFail($id);
        // Check if sector has schools
        if ($sector->schools()->count() > 0) {
            throw new \Exception('Bu sektora aid məktəblər var. Əvvəlcə məktəbləri silin.');
        }
        // Remove admin if exists
        if ($sector->admin) {
            $sector->admin->delete();
        }
        $sector->delete();
    }

    public function assignAdmin(int $sectorId, array $data)
    {
        $sector = Sector::findOrFail($sectorId);
        
        // Generate random password
        $password = Str::random(10);
        
        // Create admin user
        $admin = User::create([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => bcrypt($password),
            'role' => 'sectoradmin'
        ]);

        // Assign to sector
        $sector->admin()->associate($admin);
        $sector->save();

        // Send credentials if requested
        if (isset($data['send_credentials']) && $data['send_credentials']) {
            Mail::to($admin->email)->send(new AdminCredentials($admin, $password));
        }

        return $admin;
    }

    /**
     * Sektora aid məktəblərin sayını əldə edir
     */
    public function getSchoolsCount(int $sectorId): int
    {
        $sector = Sector::findOrFail($sectorId);
        return $sector->schools()->count();
    }

    /**
     * Sektora aid məktəbləri əldə edir
     */
    public function getSchools(int $sectorId)
    {
        $sector = Sector::findOrFail($sectorId);
        return $sector->schools()->with('admin')->get();
    }

    /**
     * Sektor adminini yeniləyir
     */
    public function updateAdmin(int $sectorId, array $data)
    {
        $sector = Sector::findOrFail($sectorId);
        
        if (!$sector->admin) {
            throw new \Exception('Bu sektorun admini yoxdur.');
        }

        $admin = $sector->admin;
        $admin->update([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone']
        ]);

        // Şifrə yeniləməsi tələb olunursa
        if (isset($data['password']) && $data['password']) {
            $admin->update(['password' => bcrypt($data['password'])]);
            
            // Yeni məlumatları email ilə göndər
            if (isset($data['send_credentials']) && $data['send_credentials']) {
                Mail::to($admin->email)->send(new AdminCredentials($admin, $data['password']));
            }
        }

        return $admin;
    }

    /**
     * Sektor adminini silir
     */
    public function removeAdmin(int $sectorId)
    {
        $sector = Sector::findOrFail($sectorId);
        
        if (!$sector->admin) {
            throw new \Exception('Bu sektorun admini yoxdur.');
        }

        $admin = $sector->admin;
        $sector->admin()->dissociate();
        $sector->save();
        $admin->delete();

        return true;
    }

    /**
     * Sektorun statistikasını əldə edir
     */
    public function getStats(int $sectorId): array
    {
        $sector = Sector::findOrFail($sectorId);
        
        return [
            'schools_count' => $sector->schools()->count(),
            'active_schools' => $sector->schools()->where('status', 'active')->count(),
            'pending_schools' => $sector->schools()->where('status', 'pending')->count(),
            'has_admin' => $sector->admin ? true : false,
            'region_name' => $sector->region->name,
            'created_at' => $sector->created_at->format('d.m.Y'),
            'updated_at' => $sector->updated_at->format('d.m.Y')
        ];
    }
}