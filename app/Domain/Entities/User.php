<?php

namespace App\Domain\Entities;

use App\Domain\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use App\Domain\Entities\Region;
use App\Domain\Entities\Sector;
use App\Domain\Entities\School;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

    
class User extends Authenticatable implements MustVerifyEmail
{
    use SoftDeletes, HasApiTokens, HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\UserFactory::new();
    }

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'username',
        'password',
        'utis_code',
        'user_type',
        'region_id',
        'sector_id',
        'school_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'user_type' => UserType::class,
    ];

    // Region ilə əlaqə
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    // Sector ilə əlaqə
    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    // Məktəb ilə əlaqə
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    // Password-u hash-ləmək üçün mutator
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    // İstifadəçinin SuperAdmin olub-olmadığını yoxlamaq
    public function isSuperAdmin(): bool
    {
        return $this->user_type === UserType::SUPER_ADMIN;
    }

    // İstifadəçinin SectorAdmin olub-olmadığını yoxlamaq
    public function isSectorAdmin(): bool
    {
        return $this->user_type === UserType::SECTOR_ADMIN;
    }

    // İstifadəçinin SchoolAdmin olub-olmadığını yoxlamaq
    public function isSchoolAdmin(): bool
    {
        return $this->user_type === UserType::SCHOOL_ADMIN;
    }

    // İstifadəçinin SectorAdmin yarada biləcəyini yoxlamaq
    public function canCreateSectorAdmin(): bool
    {
        return $this->isSuperAdmin();
    }

    // İstifadəçinin SchoolAdmin yarada biləcəyini yoxlamaq
    public function canCreateSchoolAdmin(): bool
    {
        return $this->isSuperAdmin() || $this->isSectorAdmin();
    }

    // Login zamanını yeniləmək
    public function updateLastLoginTime(): void
    {
        $this->last_login_at = now();
        $this->save();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    // Role yoxlama metodu
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    // Permission yoxlama metodu
    public function hasPermission(string $permission): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('slug', $permission);
            })->exists();
    }
}