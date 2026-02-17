<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can ban the model.
     */
    public function ban(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can access/manage admin/asesor profile.
     */
    public function manageAdminProfile(User $user): bool
    {
        return $user->hasRole(['admin', 'asesor']);
    }

    /**
     * Determine whether the user can access/manage asesi profile.
     */
    public function manageAsesiProfile(User $user): bool
    {
        return $user->hasRole('asesi');
    }
}
