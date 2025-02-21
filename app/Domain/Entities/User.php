<?php

namespace App\Domain\Entities;

use App\Domain\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

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
        'last_login_at' => 'datetime',
        'user_type' => 'string',
        'is_active' => 'boolean',
        'region_id' => 'integer',
        'sector_id' => 'integer',
        'school_id' => 'integer'
    ];

    /**
     * The relationships that should be eager loaded.
     *
     * @var array
     */
    protected $with = ['roles', 'permissions'];

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
     * Get the roles that belong to the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Check if user has the given role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role): bool
    {
        return $this->roles->contains('slug', $role);
    }

    /**
     * Get all permissions via roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function permissions()
    {
        return $this->hasManyThrough(
            Permission::class,
            Role::class,
            'id', // Role table foreign key
            'id', // Permission table foreign key
            'id', // User table local key
            'id'  // Role table local key
        )
        ->join('role_permissions', function($join) {
            $join->on('permissions.id', '=', 'role_permissions.permission_id')
                 ->on('roles.id', '=', 'role_permissions.role_id');
        })
        ->join('user_roles', function($join) {
            $join->on('roles.id', '=', 'user_roles.role_id')
                 ->where('user_roles.user_id', '=', $this->id);
        })
        ->select('permissions.*')
        ->distinct();
    }

    /**
     * Check if user has a specific permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        return Cache::remember('user_permission_'.$this->id.'_'.$permission, 60, function () use ($permission) {
            return $this->permissions()->where('permissions.slug', $permission)->exists();
        });
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
        return $this->user_type === 'superadmin';
    }

    /**
     * Check if user is a sector admin.
     *
     * @return bool
     */
    public function isSectorAdmin(): bool
    {
        return $this->user_type === 'sector-admin';
    }

    /**
     * Check if user is a school admin.
     *
     * @return bool
     */
    public function isSchoolAdmin(): bool
    {
        return $this->user_type === 'school-admin';
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