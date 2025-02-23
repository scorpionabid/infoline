<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolData extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'category_id',
        'content',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    /**
     * Bu məlumatın aid olduğu məktəb
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Bu məlumatın aid olduğu kateqoriya
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function getStatusColor()
    {
        return match($this->status) {
            'completed' => 'success',
            'pending' => 'warning',
            'expired' => 'danger',
            default => 'secondary'
        };
    }

    public function getStatusText()
    {
        return match($this->status) {
            'completed' => 'Tamamlanıb',
            'pending' => 'Gözləyir',
            'expired' => 'Vaxtı keçib',
            default => 'Naməlum'
        };
    }
}