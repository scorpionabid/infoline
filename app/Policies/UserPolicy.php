<?php

namespace App\Policies;

use App\Domain\Entities\{User, School};
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the given user can create school admins.
     *
     * @param User $user
     * @param School $school
     * @return bool
     */
    public function create(User $user, School $school): bool
    {
        // Super admin can create school admins for any school
        if ($user->hasRole('super')) {
            return true;
        }

        // Sector admin can only create school admins for schools in their sector
        if ($user->hasRole('sector_admin')) {
            return $user->sector_id === $school->sector_id;
        }

        return false;
    }
}
