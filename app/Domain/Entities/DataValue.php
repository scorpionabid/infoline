<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DataValue extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'data_values';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'column_id',
        'school_id',
        'value',
        'status',
        'updated_by',
        'comment'
    ];

    protected $attributes = [
        'status' => 'draft'
    ];

    protected static function newFactory()
    {
        return \Database\Factories\DataValueFactory::new();
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function column(): BelongsTo
    {
        return $this->belongsTo(Column::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function isValidValue(): bool
    {
        switch ($this->column->data_type) {
            case 'number':
                return is_numeric($this->value);
            case 'date':
                return strtotime($this->value) !== false;
            case 'select':
            case 'multiselect':
                $validChoices = $this->column->choices->pluck('value')->toArray();
                $values = explode(',', $this->value);
                return empty(array_diff($values, $validChoices));
            default:
                return true;
        }
    }

    public function submit(): void
    {
        if (!$this->isValidValue()) {
            throw new \InvalidArgumentException('Daxil edilən məlumat düzgün formatda deyil');
        }
        
        $this->status = 'submitted';
        $this->save();
    }

    public function approve(): void
    {
        if ($this->status !== 'submitted') {
            throw new \InvalidArgumentException('Yalnız göndərilmiş məlumatlar təsdiq edilə bilər');
        }
        
        $this->status = 'approved';
        $this->save();
    }

    public function reject(string $comment): void
    {
        if ($this->status !== 'submitted') {
            throw new \InvalidArgumentException('Yalnız göndərilmiş məlumatlar rədd edilə bilər');
        }
        
        $this->status = 'rejected';
        $this->comment = $comment;
        $this->save();
    }

    public function updateValue(string $value, int $userId): void
    {
        $this->value = $value;
        $this->updated_by = $userId;
        $this->status = 'draft';
        $this->save();
    }
}