<?php

namespace App\Domain\Entities;

use Database\Factories\SchoolFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class School extends Model
{
    use SoftDeletes,
        HasFactory;

    protected $fillable = [
        'name',
        'utis_code',
        'phone',
        'email',
        'sector_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];
    protected static function newFactory()
    {
        return SchoolFactory::new();
    }

    // Sektor ilə əlaqə (many-to-one)
    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    // Region ilə əlaqə (through sector)
    public function region(): BelongsTo
    {
        return $this->sector->region();
    }

    // Məktəb administratorları ilə əlaqə
    public function admins(): HasMany
    {
        return $this->hasMany(User::class)->where('user_type', 'schooladmin');
    }
    public function hasAdmins(): bool
    {
        return $this->admins()->exists();
    }

    // Məktəbin tam adını qaytarır (Region + Sektor + Məktəb)
    public function getFullNameAttribute(): string
    {
        return sprintf(
            '%s, %s, %s',
            $this->sector->region->name,
            $this->sector->name,
            $this->name
        );
    }
}