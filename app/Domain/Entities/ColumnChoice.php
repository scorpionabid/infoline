<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ColumnChoice extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'value',
        'column_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected static function newFactory()
    {
        return \Database\Factories\ColumnChoiceFactory::new();
    }

    public function column(): BelongsTo
    {
        return $this->belongsTo(Column::class);
    }
}