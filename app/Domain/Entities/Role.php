<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
   use SoftDeletes, HasFactory;

   protected $fillable = [
       'name',        // Rol adı: SuperAdmin, SectorAdmin, SchoolAdmin
       'slug',        // Texniki ad: super-admin, sector-admin, school-admin
       'description', // Açıqlama: Tam səlahiyyətli admin və s.
       'is_system'   // Sistem rolu olub-olmadığı (default rollar üçün)
   ];

   protected $casts = [
       'is_system' => 'boolean',
       'created_at' => 'datetime',
       'updated_at' => 'datetime',
       'deleted_at' => 'datetime'
   ];

   protected static function newFactory()
   {
       return \Database\Factories\RoleFactory::new();
   }

   // İstifadəçilərlə əlaqə (many-to-many)
   public function users(): BelongsToMany
   {
       return $this->belongsToMany(User::class, 'user_roles');
   }

   // İcazələrlə əlaqə (many-to-many)
   public function permissions(): BelongsToMany
   {
       return $this->belongsToMany(Permission::class, 'role_permissions');
   }

   // Rolun sistem rolu olub-olmadığını yoxlamaq
   public function isSystem(): bool
   {
       return $this->is_system;
   }

   // Rola icazə əlavə etmək
   public function givePermissionTo(Permission $permission): void
   {
       $this->permissions()->syncWithoutDetaching($permission->id);
   }

   // Roldan icazəni silmək - DÜZƏLDİLMİŞ VERSİYA
   public function revokePermissionTo(Permission $permission): void
   {
       $this->permissions()->detach($permission->id);
       $this->unsetRelation('permissions');
   }

   // Rolun müəyyən icazəyə malik olub-olmadığını yoxlamaq - DÜZƏLDİLMİŞ VERSİYA
   public function hasPermission(string $permission): bool
   {
       return $this->permissions()->where('slug', $permission)->exists();
   }
}