<?php

namespace App\Domain\Entities;

use Database\Factories\RegionFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Region extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'phone'
    ];
    protected static function newFactory()
    {
        return RegionFactory::new();
    }

    // Sector ilə əlaqə (one-to-many)
    public function sectors(): HasMany
    {
        return $this->hasMany(Sector::class);
    }

    // Region-a aid olan bütün məktəbləri əldə etmək üçün
    public function schools(): HasMany
    {
        return $this->hasManyThrough(School::class, Sector::class);
    }
}
