<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryField extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'label',
        'type',
        'options',
        'validation',
        'is_required',
        'is_visible',
        'order',
        'category_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_required' => 'boolean',
        'is_visible' => 'boolean',
        'options' => 'array',
        'validation' => 'array'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'is_required' => false,
        'is_visible' => true,
        'order' => 0
    ];

    /**
     * Get the category that owns the field.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
