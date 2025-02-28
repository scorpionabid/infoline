<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryAssignment extends Model
{
    protected $fillable = ['category_id', 'assigned_type', 'assigned_id'];
    
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    
    public function assigned()
    {
        if ($this->assigned_type === 'sector') {
            return $this->belongsTo(Sector::class, 'assigned_id');
        } elseif ($this->assigned_type === 'school') {
            return $this->belongsTo(School::class, 'assigned_id');
        }
        
        return null;
    }
    // app/Models/CategoryAssignment.php
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId)
            ->orWhereHas('sector', function($q) use ($schoolId) {
                $q->whereHas('schools', function($q3) use ($schoolId) {
                    $q3->where('id', $schoolId);
                });
            });
    }
}