<?php

namespace App\Policies;

use App\Models\MaintenanceRequest;
use App\Models\User;

class MaintenanceRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'board_member', 'accountant', 'resident', 'service_provider']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('service_provider')) {
            return $maintenanceRequest->assigned_to_user_id === $user->id;
        }

        if ($user->hasRole(['board_member', 'accountant'])) {
            return $user->community_id === $maintenanceRequest->community_id;
        }

        if ($user->hasRole('resident')) {
            return $maintenanceRequest->created_by_user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'board_member', 'resident']);
    }

    /**
     * Determine whether the user can assign a provider.
     */
    public function assign(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('board_member')) {
            return $user->community_id === $maintenanceRequest->community_id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the status.
     */
    public function updateStatus(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('board_member')) {
            return $user->community_id === $maintenanceRequest->community_id;
        }

        if ($user->hasRole('service_provider')) {
             // Provider can only close an assigned request that is in progress
             return $maintenanceRequest->assigned_to_user_id === $user->id
                 && $maintenanceRequest->status === MaintenanceRequest::STATUS_IN_PROGRESS;
        }

        return false;
    }
}
