<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use SoftDeletes, HasFactory;

    private const SECTOR_PERMISSIONS = [
        'manage_sector_data' => 'Sektor məlumatlarını idarə etmək',
        'assign_school_admins' => 'Məktəb adminləri təyin etmək',
        'view_sector_reports' => 'Sektor hesabatlarını görmək',
        'manage_sector_settings' => 'Sektor parametrlərini idarə etmək'
    ];

    private const SCHOOL_PERMISSIONS = [
        'manage_school_data' => 'Məktəb məlumatlarını idarə etmək',
        'assign_sector_admins' => 'Sektor adminləri təyin etmək',
        'view_school_reports' => 'Məktəb hesabatlarını görmək',
        'manage_school_settings' => 'Məktəb parametrlərini idarə etmək'
    ];

    protected $fillable = [
        'name',        // İcazənin adı: Məktəb admin idarəetmə
        'slug',        // Texniki ad: manage-school-admins
        'description', // Açıqlama: Məktəb adminlərini yaratmaq, dəyişmək və silmək
        'group'        // Qrupu: admin-management, school-management və s.
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Factory üçün metod əlavə edildi
    protected static function newFactory()
    {
        return \Database\Factories\PermissionFactory::new();
    }

    // Rollarla əlaqə (many-to-many)
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    // İstifadəçilərlə əlaqə (many-to-many through roles)
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_permissions');
    }

    // İcazənin müəyyən qrupa aid olub-olmadığını yoxlamaq
    public function isInGroup(string $groupName): bool
    {
        return $this->group === $groupName;
    }
}