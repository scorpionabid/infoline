<?php

namespace App\Domain\Entities;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends SpatieRole
{
    use HasFactory;

    protected $fillable = [
        'name',        // Rol adı: SuperAdmin, SectorAdmin, SchoolAdmin
        'guard_name',  // Guard name for the role
        'description', // Açıqlama: Tam səlahiyyətli admin və s.
        'is_system'   // Sistem rolu olub-olmadığı (default rollar üçün)
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected static function newFactory()
    {
        return \Database\Factories\RoleFactory::new();
    }

    // Rolun sistem rolu olub-olmadığını yoxlamaq
    public function isSystem(): bool
    {
        return $this->is_system;
    }

    // For backward compatibility
    public function getSlugAttribute(): string
    {
        return $this->name;
    }
}