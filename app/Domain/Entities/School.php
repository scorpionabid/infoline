<?php

namespace App\Domain\Entities;

use Database\Factories\SchoolFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domain\Entities\Category;
use Illuminate\Support\Facades\Log;

class School extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'utis_code',
        'phone',
        'email',
        'sector_id',
        'admin_id',
        'address',
        'website',
        'status',
        'type',
        'description'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'status' => 'boolean'
    ];

    protected $with = ['sector', 'admin'];

    protected static function newFactory()
    {
        return SchoolFactory::new();
    }

    /**
     * Calculate data completion percentage
     *
     * @return array
     */
    public function calculateDataCompletion(): array
    {
        $this->loadMissing('data.category.fields');

        $totalFields = 0;
        $completedFields = 0;
        $categoryCompletion = [];

        foreach ($this->data as $data) {
            $category = $data->category;
            $fields = $category->fields;

            if ($fields->isEmpty()) {
                continue;
            }

            $categoryTotal = $fields->count();
            $categoryCompleted = 0;

            foreach ($fields as $field) {
                $totalFields++;
                if (!empty($data->data[$field->name])) {
                    $completedFields++;
                    $categoryCompleted++;
                }
            }

            $categoryCompletion[$category->name] = [
                'total' => $categoryTotal,
                'completed' => $categoryCompleted,
                'percentage' => $categoryTotal > 0 ? round(($categoryCompleted / $categoryTotal) * 100) : 0
            ];
        }

        return [
            'total' => $totalFields,
            'completed' => $completedFields,
            'percentage' => $totalFields > 0 ? round(($completedFields / $totalFields) * 100) : 0,
            'categories' => $categoryCompletion
        ];
    }

    // Sektor ilə əlaqə (many-to-one)
    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    // Region ilə əlaqə (through sector)
    public function region(): BelongsTo
    {
        return $this->sector->region();
    }

    // Məktəb administratoru ilə əlaqə
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Məktəb administratorları ilə əlaqə
    public function admins(): HasMany
    {
        return $this->hasMany(User::class)->where('user_type', 'schooladmin');
    }

    /**
     * Məktəbin məlumatları
     */
    public function data()
    {
        return $this->hasMany(SchoolData::class);
    }

    // Məktəbin tam adını əldə etmək üçün (Sektor və Region adı ilə birlikdə)
    public function getFullNameAttribute(): string
    {
        return "{$this->sector->region->name} - {$this->sector->name} - {$this->name}";
    }

    // Məktəbin statusunu yoxlamaq üçün
    public function isActive(): bool
    {
        return $this->status && !$this->trashed();
    }

    // Məktəbin son dəfə məlumat daxil etdiyi tarixi əldə etmək üçün
    public function getLastDataEntryAttribute()
    {
        return $this->data()->latest()->first()?->created_at;
    }

    // Məktəbin məlumat doldurma faizini hesablamaq üçün
    public function getDataCompletionPercentageAttribute(): int
    {
        try {
            $totalFields = Category::sum('field_count') ?: 1; // Default to 1 to avoid division by zero
            $filledFields = $this->data()->count();
            $percentage = ($filledFields / $totalFields) * 100;
            return min(100, max(0, (int) $percentage)); // Ensure percentage is between 0 and 100
        } catch (\Exception $e) {
            Log::error('Error calculating data completion percentage', [
                'school_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    // Məktəbə admin təyin etmək üçün
    public function assignAdmin(User $user): void
    {
        $this->update(['admin_id' => $user->id]);
        $user->update(['school_id' => $this->id]);
    }

    // Məktəbin aktiv olub olmadığını yoxlamaq üçün scope
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // Məktəbin tipinə görə filtirləmək üçün scope
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // Məktəbin regionuna görə filtirləmək üçün scope
    public function scopeInRegion($query, $regionId)
    {
        return $query->whereHas('sector', function ($q) use ($regionId) {
            $q->where('region_id', $regionId);
        });
    }
}