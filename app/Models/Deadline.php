<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deadline extends Model
{
    protected $fillable = [
        'category_id',
        'deadline',
        'warning_days',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'warning_days' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}