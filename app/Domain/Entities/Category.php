<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => true
    ];

    /**
     * Get the columns for the category.
     */
    public function columns(): HasMany
    {
        return $this->hasMany(Column::class)->orderBy('order');
    }

    /**
     * Check if the category has any active columns.
     */
    public function hasActiveColumns(): bool
    {
        return $this->columns()->where('status', true)->exists();
    }

    /**
     * Get the number of active columns.
     */
    public function getActiveColumnsCount(): int
    {
        return $this->columns()->where('status', true)->count();
    }

    /**
     * Check if the category has any data.
     */
    public function hasData(): bool
    {
        return $this->columns()->whereHas('dataValues')->exists();
    }

    /**
     * Get the validation rules for all columns.
     */
    public function getValidationRules(): array
    {
        $rules = [];

        foreach ($this->columns as $column) {
            if ($column->status) {
                $rules[$column->name] = implode('|', $column->getValidationRules());
            }
        }

        return $rules;
    }
}