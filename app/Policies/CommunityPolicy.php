<?php

namespace App\Policies;

use App\Models\Community;
use App\Models\User;

class CommunityPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Community $community): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('service_provider')) {
            return false;
        }

        // Staff Scoping (Board, Accountant)
        if ($user->community_id === $community->id && ($user->hasRole('board_member') || $user->hasRole('accountant'))) {
            return true;
        }

        // Resident Scoping (via Units)
        if ($user->hasRole('resident')) {
            // Check if user has any unit in this community
            return $user->units()->where('community_id', $community->id)->exists();
        }

        return false;
    }
}
