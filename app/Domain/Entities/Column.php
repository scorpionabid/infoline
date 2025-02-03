<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Column extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'data_type',
        'end_date',
        'input_limit',
        'category_id'
    ];

    protected $casts = [
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected static function newFactory()
    {
        return \Database\Factories\ColumnFactory::new();
    }

    // Kateqoriya ilə əlaqə (many-to-one)
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Seçim variantları ilə əlaqə (one-to-many)
    public function choices(): HasMany
    {
        return $this->hasMany(ColumnChoice::class);
    }

    // Sütunun aktiv olub-olmadığını yoxlamaq
    public function isActive(): bool
    {
        if ($this->end_date === null) {
            return true;
        }
        return !$this->end_date->isPast();
    }

    // Data tipinin düzgün olub-olmadığını yoxlamaq
    public function isValidDataType(): bool
    {
        return in_array($this->data_type, [
            'text',
            'number',
            'date',
            'select',
            'multiselect',
            'file'
        ]);
    }

    // Seçim variantlarının tələb olunub-olunmadığını yoxlamaq
    public function requiresChoices(): bool
    {
        return in_array($this->data_type, ['select', 'multiselect']);
    }
}
