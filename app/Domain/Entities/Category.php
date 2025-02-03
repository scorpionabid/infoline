<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name'
    ];

    protected static function newFactory()
    {
        return \Database\Factories\CategoryFactory::new();
    }

    // Sütunlarla əlaqə (one-to-many)
    public function columns(): HasMany
    {
        return $this->hasMany(Column::class);
    }

    // Aktiv sütunları əldə etmək üçün
    public function activeColumns(): HasMany
    {
        return $this->hasMany(Column::class)
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>', now());
            });
    }

    // Əlavə metod: aktiv sütunları attribute kimi əldə etmək üçün
    public function getActiveColumnsAttribute()
    {
        return $this->activeColumns()->get();
    }
}