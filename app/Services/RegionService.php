<?php

namespace App\Services;

use App\Domain\Entities\Region;
use App\Domain\Entities\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegionService
{
    public function getStatistics(Region $region)
    {
        return [
            'sectors_count' => $region->sectors()->count(),
            'schools_count' => $region->schools()->count(),
            'active_sectors_count' => $region->active_sectors_count,
            'active_schools_count' => $region->active_schools_count
        ];
    }

    public function updateAdmin(Region $region, array $data)
    {
        DB::beginTransaction();
        try {
            // Əgər admin varsa və yeni admin təyin edilirsə
            if ($region->admin_id && isset($data['admin_id'])) {
                $oldAdmin = $region->admin;
                $oldAdmin->update(['role' => 'user']);
            }

            // Yeni admin yaradılması
            if (!isset($data['admin_id'])) {
                $password = Str::random(10);
                $admin = User::create([
                    'full_name' => $data['full_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'password' => Hash::make($password),
                    'role' => 'region_admin'
                ]);
                $data['admin_id'] = $admin->id;

                if (isset($data['send_credentials']) && $data['send_credentials']) {
                    $this->sendAdminCredentials($admin, $password, $region);
                }
            }

            $region->update(['admin_id' => $data['admin_id']]);
            DB::commit();

            return [
                'success' => true,
                'message' => 'Region admini uğurla yeniləndi'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Xəta baş verdi: ' . $e->getMessage()
            ];
        }
    }

    public function removeAdmin(Region $region)
    {
        DB::beginTransaction();
        try {
            if ($region->admin_id) {
                $admin = $region->admin;
                $admin->update(['role' => 'user']);
                $region->update(['admin_id' => null]);
            }

            DB::commit();
            return [
                'success' => true,
                'message' => 'Region admini uğurla silindi'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Xəta baş verdi: ' . $e->getMessage()
            ];
        }
    }

    protected function sendAdminCredentials(User $admin, string $password, Region $region)
    {
        $data = [
            'full_name' => $admin->full_name,
            'email' => $admin->email,
            'password' => $password,
            'region' => $region->name
        ];

        Mail::send('emails.admin-credentials', $data, function($message) use ($admin) {
            $message->to($admin->email, $admin->full_name)
                    ->subject('Region Admin Giriş Məlumatları');
        });
    }
}