<?php

namespace App\Providers;

use App\Domain\Entities\{School, User};
use App\Domain\Enums\UserType;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Domain\Entities\User' => 'App\Policies\UserPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Super Admin has all permissions
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('super')) {
                return true;
            }
        });

        // Manage schools permission
        Gate::define('manage-schools', function (User $user) {
            return in_array($user->user_type, [
                UserType::SUPER_ADMIN->value,
                UserType::SECTOR_ADMIN->value
            ]);
        });

        // Assign admin permission
        Gate::define('assign-admin', function (User $user) {
            return in_array($user->user_type, [
                UserType::SUPER_ADMIN->value,
                UserType::SECTOR_ADMIN->value
            ]);
        });

        // Manage school data permission
        Gate::define('manage-school-data', function (User $user, School $school) {
            return $user->user_type === UserType::SCHOOL_ADMIN->value && $school->admin_id === $user->id;
        });

        // View school data permission
        Gate::define('view-school-data', function (User $user, School $school) {
            return $user->user_type === UserType::SCHOOL_ADMIN->value && $school->admin_id === $user->id;
        });

        // Manage regions permission
        Gate::define('manage-regions', function (User $user) {
            return $user->user_type === UserType::SUPER_ADMIN->value;
        });

        // Manage sectors permission
        Gate::define('manage-sectors', function (User $user) {
            return $user->user_type === UserType::SUPER_ADMIN->value;
        });

        // Manage users permission
        Gate::define('manage-users', function (User $user) {
            return $user->user_type === UserType::SUPER_ADMIN->value;
        });

        // View reports permission
        Gate::define('view-reports', function (User $user) {
            return in_array($user->user_type, [
                UserType::SUPER_ADMIN->value,
                UserType::SECTOR_ADMIN->value
            ]);
        });
    }
}
