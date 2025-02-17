<?php

namespace App\Domain\Entities;

use App\Domain\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use SoftDeletes, 
        HasApiTokens, 
        HasFactory, 
        Notifiable;

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Database\Factories\UserFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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
        'last_login_at',
        'last_login_ip',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'user_type' => UserType::class,
        'last_login_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * Relationship: Region
     *
     * @return BelongsTo
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Relationship: Sector
     *
     * @return BelongsTo
     */
    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    /**
     * Relationship: School
     *
     * @return BelongsTo
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Relationship: Roles
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Relationship: Notifications
     *
     * @return HasMany
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Set password attribute with hashing.
     *
     * @param string $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Get full name attribute.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Check if user is a super admin.
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->user_type === UserType::SUPER_ADMIN;
    }

    /**
     * Check if user is a sector admin.
     *
     * @return bool
     */
    public function isSectorAdmin(): bool
    {
        return $this->user_type === UserType::SECTOR_ADMIN;
    }

    /**
     * Check if user is a school admin.
     *
     * @return bool
     */
    public function isSchoolAdmin(): bool
    {
        return $this->user_type === UserType::SCHOOL_ADMIN;
    }

    /**
     * Determine if user can create sector admins.
     *
     * @return bool
     */
    public function canCreateSectorAdmin(): bool
    {
        return $this->isSuperAdmin();
    }

    /**
     * Determine if user can create school admins.
     *
     * @return bool
     */
    public function canCreateSchoolAdmin(): bool
    {
        return $this->isSuperAdmin() || $this->isSectorAdmin();
    }

    /**
     * Check if user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        // Convert role name to slug format if needed
        $roleSlug = str_replace('_', '-', $role);
        
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    /**
     * Check if user has a specific permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('slug', $permission);
            })->exists();
    }

    /**
     * Update login information.
     *
     * @param string|null $ip
     * @return void
     */
    public function updateLoginInformation(?string $ip = null): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip
        ]);
    }

    /**
     * Check if user is inactive.
     *
     * @param int $days
     * @return bool
     */
    public function isInactive(int $days = 90): bool
    {
        return $this->last_login_at === null || 
               $this->last_login_at->diffInDays(now()) > $days;
    }

    /**
     * Scope to find inactive users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $days
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query, int $days = 90)
    {
        return $query->where(function ($q) use ($days) {
            $q->whereNull('last_login_at')
              ->orWhere('last_login_at', '<=', now()->subDays($days));
        });
    }

    /**
     * Activate user account.
     *
     * @return void
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate user account.
     *
     * @return void
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
    
}