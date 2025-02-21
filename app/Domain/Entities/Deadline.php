<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deadline extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'category_id',
        'status',
        'priority',
        'created_by',
        'assigned_to'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected $with = ['category', 'creator', 'assignee'];

    // Deadline-ın aid olduğu kateqoriya
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Deadline-ı yaradan istifadəçi
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Deadline təyin edilən istifadəçi
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Deadline-ın gecikib-gecikmədiyini yoxlamaq üçün
    public function isOverdue(): bool
    {
        return !$this->status && now()->greaterThan($this->due_date);
    }

    // Deadline-ın tamamlanıb-tamamlanmadığını yoxlamaq üçün
    public function isCompleted(): bool
    {
        return $this->status;
    }

    // Aktiv deadline-ları filtirləmək üçün scope
    public function scopeActive($query)
    {
        return $query->where('status', false)
                    ->where('due_date', '>=', now());
    }

    // Gecikmiş deadline-ları filtirləmək üçün scope
    public function scopeOverdue($query)
    {
        return $query->where('status', false)
                    ->where('due_date', '<', now());
    }

    // Prioritetə görə sıralamaq üçün scope
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    // Tarixə görə sıralamaq üçün scope
    public function scopeByDueDate($query)
    {
        return $query->orderBy('due_date', 'asc');
    }
}