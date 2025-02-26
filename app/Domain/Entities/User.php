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
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use SoftDeletes, 
        HasApiTokens, 
        HasFactory, 
        Notifiable,
        \Spatie\Permission\Traits\HasRoles;

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
        'password',
        'utis_code',
        'user_type',
        'role',
        'region_id',
        'sector_id',
        'school_id',
        'last_login_at',
        'last_login_ip',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'region_id' => 'integer',
        'sector_id' => 'integer',
        'school_id' => 'integer',
        'user_type' => UserType::class,
        'utis_code' => 'string'
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
     * The relationships that should be eager loaded.
     *
     * @var array
     */
    protected $with = ['roles'];

    /**
     * Get the region that owns the user.
     *
     * @return BelongsTo
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }



    /**
     * Get the sector that owns the user.
     *
     * @return BelongsTo
     */
    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    /**
     * Get the school that owns the user.
     *
     * @return BelongsTo
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
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
    /**
     * Get all schools where this user is an admin.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schools(): HasMany
    {
        return $this->hasMany(School::class, 'admin_id');
    }
}