<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can approve another user.
     */
    public function approve(User $actor, User $target): bool
    {
        // Only admins can approve
        if (!$actor->hasRole('admin')) {
            return false;
        }

        // Admins cannot approve themselves (prevent footgun)
        if ($actor->id === $target->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can reject another user.
     */
    public function reject(User $actor, User $target): bool
    {
        // Only admins can reject
        if (!$actor->hasRole('admin')) {
            return false;
        }

        // Admins cannot reject themselves (prevent footgun)
        if ($actor->id === $target->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can manage users (view index, edit, update).
     */
    public function manage(User $actor): bool
    {
        return $actor->hasRole('admin');
    }

    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $actor): bool
    {
        return $actor->hasRole('admin');
    }

    /**
     * Determine whether the user can update the given user.
     */
    public function update(User $actor, User $target): bool
    {
        return $actor->hasRole('admin');
    }
}
