<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Column extends Model
{
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
        'status',
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
        'status' => 'boolean',
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
        'status' => true,
        'options' => '[]',
        'validation_rules' => '[]',
        'order' => 1
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
    public function dataValues(): HasMany
    {
        return $this->hasMany(DataValue::class);
    }

    /**
     * Check if the column has expired.
     */
    public function hasExpired(): bool
    {
        if (!$this->end_date) {
            return false;
        }

        return Carbon::now()->isAfter($this->end_date);
    }

    /**
     * Check if the column has reached its input limit.
     */
    public function hasReachedLimit(): bool
    {
        if (!$this->input_limit) {
            return false;
        }

        return $this->dataValues()->count() >= $this->input_limit;
    }

    /**
     * Get the validation rules for the column.
     */
    public function getValidationRules(): array
    {
        $rules = [];

        // Əsas qaydalar
        if ($this->required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        // Növə görə qaydalar
        switch ($this->type) {
            case 'text':
            case 'textarea':
                $rules[] = 'string';
                if (isset($this->validation_rules['min_length'])) {
                    $rules[] = 'min:' . $this->validation_rules['min_length'];
                }
                if (isset($this->validation_rules['max_length'])) {
                    $rules[] = 'max:' . $this->validation_rules['max_length'];
                }
                break;

            case 'number':
                $rules[] = 'numeric';
                if (isset($this->validation_rules['min'])) {
                    $rules[] = 'min:' . $this->validation_rules['min'];
                }
                if (isset($this->validation_rules['max'])) {
                    $rules[] = 'max:' . $this->validation_rules['max'];
                }
                break;

            case 'date':
                $rules[] = 'date';
                break;

            case 'select':
                $rules[] = 'string';
                if (!empty($this->options)) {
                    $rules[] = 'in:' . implode(',', $this->options);
                }
                break;
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
