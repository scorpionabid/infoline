<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
        'settings',
        'is_active',
        'order',
        'parent_id',
        'field_count'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'is_active' => true,
        'order' => 0
    ];

    /**
     * Get the fields for the category.
     */
    public function fields(): HasMany
    {
        return $this->hasMany(CategoryField::class);
    }

    /**
     * Get the columns for the category.
     */
    public function columns(): HasMany
    {
        return $this->hasMany(Column::class)->orderBy('order');
    }

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Check if the category has any active columns.
     */
    public function hasActiveColumns(): bool
    {
        return $this->columns()->where('is_active', true)->exists();
    }

    /**
     * Get active columns for the category.
     */
    public function getActiveColumns(): HasMany
    {
        return $this->columns()->where('is_active', true)->orderBy('order');
    }
}