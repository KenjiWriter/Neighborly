<?php

namespace App\Policies;

use App\Models\FinanceEntry;
use App\Models\User;

class FinanceEntryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Residents: Denied generic "viewAny" (which implies list of entries).
        // Residents can only view "Overview" which is aggregated and handled via Controller/Capability.
        // Board/Accountant/Admin: Allowed (scoped).
        return $user->hasRole(['admin', 'board_member', 'accountant']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FinanceEntry $financeEntry): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole(['board_member', 'accountant'])) {
            return $user->community_id === $financeEntry->community_id;
        }

        // Resident: Denied viewing individual entries.
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'accountant']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FinanceEntry $financeEntry): bool
    {
         if ($user->hasRole('admin')) {
            return true;
        }
        
        if ($user->hasRole('accountant')) {
            return $user->community_id === $financeEntry->community_id;
        }

        return false;
    }
}
