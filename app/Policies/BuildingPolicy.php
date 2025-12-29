<?php

namespace App\Policies;

use App\Models\Building;
use App\Models\User;

class BuildingPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Building $building): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('service_provider')) {
            return false;
        }

        // Staff Scoping
        if ($user->community_id === $building->community_id && ($user->hasRole('board_member') || $user->hasRole('accountant'))) {
            return true;
        }

        // Resident Scoping
        if ($user->hasRole('resident')) {
            // Check if user has any unit in this building OR just in this community?
            // "Resident: can view ONLY communities/buildings/units they are connected to via unit_user"
            // Usually showing all buildings in their community is fine, OR strict building access.
            // Strict interpretation: "connected to". So only THEIR building.
            // However, typical app logic allows seeing the building list of their community.
            // Let's go with strict first as per "deny by default" ethos, OR allow if they have a unit in the community?
            // "via unit_user" implies the relational path.
            // Let's check if the user has a unit in this building.
             return $user->units()->where('building_id', $building->id)->exists();
        }

        return false;
    }
}
