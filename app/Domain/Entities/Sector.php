<?php

namespace App\Domain\Entities;

use Database\Factories\SectorFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;  // düzgün import



class Sector extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'region_id',
        'admin_id',
    ];
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
}
