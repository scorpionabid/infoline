<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\NotificationFactory;

class Notification extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'type',      // system, data_update, import, export
        'title',
        'message',
        'data',      // əlavə data JSON formatında
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    protected static function newFactory()
    {
        return NotificationFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        $this->read_at = now();
        $this->save();
    }

    public function markAsUnread(): void
    {
        $this->read_at = null;
        $this->save();
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}