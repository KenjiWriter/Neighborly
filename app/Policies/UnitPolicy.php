<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;

class UnitPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Unit $unit): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('service_provider')) {
            return false;
        }

        // Staff Scoping
        if ($user->community_id === $unit->community_id && ($user->hasRole('board_member') || $user->hasRole('accountant'))) {
            return true;
        }

        // Resident Scoping
        if ($user->hasRole('resident')) {
            // Strict: Can view ONLY their own unit.
            return $user->units()->where('units.id', $unit->id)->exists();
        }

        return false;
    }
}
