<?php

namespace App\Domain\Entities;

use Database\Factories\SectorFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sector extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'region_id',
        'admin_id',
        'code',
        'description',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'status' => 'boolean'
    ];

    protected $with = ['region', 'admin', 'user'];

    protected static function newFactory()
    {
        return SectorFactory::new();
    }

    // Region ilə əlaqə (many-to-one)
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    // Məktəblərlə əlaqə (one-to-many)
    public function schools(): HasMany
    {
        return $this->hasMany(School::class);
    }

    // Sektora aid olan istifadəçilərlə əlaqə
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Sektora aid olan admin ilə əlaqə
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Sektor istifadəçisi ilə əlaqə (many-to-one)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Aktiv məktəblərin sayını əldə etmək üçün
    public function getActiveSchoolsCountAttribute(): int
    {
        return $this->schools()->whereNull('deleted_at')->count();
    }

    // Aktiv istifadəçilərin sayını əldə etmək üçün
    public function getActiveUsersCountAttribute(): int
    {
        return $this->users()->whereNull('deleted_at')->count();
    }

    // Sektorun tam adını əldə etmək üçün (Region adı ilə birlikdə)
    public function getFullNameAttribute(): string
    {
        return "{$this->region->name} - {$this->name}";
    }

    // Sektorun statusunu yoxlamaq üçün
    public function isActive(): bool
    {
        return $this->status && !$this->trashed();
    }

    // Sektora məktəb əlavə etmək üçün
    public function addSchool(School $school): void
    {
        $school->update(['sector_id' => $this->id]);
    }

    // Sektora admin təyin etmək üçün
    public function assignAdmin(User $user): void
    {
        $this->update(['admin_id' => $user->id]);
        $user->update(['sector_id' => $this->id]);
    }
}
