<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Column extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'type',
        'required',
        'options',
        'validation_rules',
        'order',
        'is_active',
        'end_date',
        'input_limit'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'required' => 'boolean',
        'is_active' => 'boolean',
        'options' => 'array',
        'validation_rules' => 'array',
        'end_date' => 'date',
        'input_limit' => 'integer'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'required' => false,
        'is_active' => true,
        'order' => 1,
        'type' => 'text'
    ];

    /**
     * Get the category that owns the column.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the data values for the column.
     */
    public function values(): HasMany
    {
        return $this->hasMany(DataValue::class);
    }

    /**
     * Check if the column is active.
     */
    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->end_date && Carbon::now()->isAfter($this->end_date)) {
            return false;
        }

        return true;
    }

    /**
     * Check if the column has reached its input limit.
     */
    public function hasReachedInputLimit(): bool
    {
        if (!$this->input_limit) {
            return false;
        }

        return $this->values()->count() >= $this->input_limit;
    }

    /**
     * Get the validation rules for the column.
     */
    public function getValidationRules(): array
    {
        $rules = ['required' => $this->required];

        if ($this->validation_rules) {
            $rules = array_merge($rules, $this->validation_rules);
        }

        if ($this->type === 'select' && $this->options) {
            $rules['in'] = array_keys($this->options);
        }

        return $rules;
    }

    /**
     * Format the value according to the column type.
     */
    public function formatValue($value)
    {
        if ($value === null) {
            return null;
        }

        switch ($this->type) {
            case 'number':
                return (float) $value;

            case 'date':
                return Carbon::parse($value)->format('Y-m-d');

            default:
                return (string) $value;
        }
    }
}
