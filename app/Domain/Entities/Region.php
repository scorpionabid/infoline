<?php

namespace App\Domain\Entities;

use Database\Factories\RegionFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Region extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'code',
        'description',
        'admin_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function newFactory()
    {
        return RegionFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($region) {
            if (empty($region->code)) {
                // Regionun adından kod generasiya et
                $code = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $region->name));
                
                // Əgər bu kod artıq varsa, sonuna rəqəm əlavə et
                $count = 1;
                $newCode = $code;
                while (static::where('code', $newCode)->exists()) {
                    $newCode = $code . $count;
                    $count++;
                }
                
                $region->code = $newCode;
            }
        });
    }

    // Region administratoru ilə əlaqə
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Sector ilə əlaqə (one-to-many)
    public function sectors(): HasMany
    {
        return $this->hasMany(Sector::class);
    }

    // Region-a aid olan bütün məktəbləri əldə etmək üçün
    public function schools(): HasManyThrough
    {
        return $this->hasManyThrough(School::class, Sector::class);
    }

    // Region-un aktiv sektorlarının sayını əldə etmək üçün
    public function getActiveSectorsCountAttribute(): int
    {
        return $this->sectors()->whereNull('deleted_at')->count();
    }

    // Region-un aktiv məktəblərinin sayını əldə etmək üçün
    public function getActiveSchoolsCountAttribute(): int
    {
        return $this->schools()->whereNull('deleted_at')->count();
    }
}
