<?php

namespace App\Policies;

use App\Models\Poll;
use App\Models\User;

class PollPolicy
{
    public function viewAny(User $user): bool
    {
        // All authenticated users can view polls list
        return true;
    }

    public function view(User $user, Poll $poll): bool
    {
        // Creator can always view
        if ($poll->created_by_user_id === $user->id) {
            return true;
        }

        // Must be in same community and poll must be active
        if ($user->hasRole(['admin', 'board_member'])) {
            return $user->community_id === $poll->community_id || $user->hasRole('admin');
        }

        // Building Manager: View if targeted at their managed buildings
        if ($user->hasRole('building_manager')) {
             if ($poll->community_id !== $user->community_id) return false;
             if ($poll->starts_at > now() || $poll->ends_at < now()) return false;
             
             return $poll->targetedBuildings()->whereIn('poll_building_visibility.building_id', $user->managedBuildings->pluck('id'))->exists();
        }

        // Residents can view active polls in their community
        return $user->community_id === $poll->community_id && 
               $poll->starts_at <= now() && 
               $poll->ends_at >= now();
    }

    public function create(User $user): bool
    {
        if ($user->hasRole(['admin', 'board_member'])) {
            return true;
        }

        if ($user->hasRole('building_manager')) {
            // Strict check on request data
            $audienceType = request('audience_type');
            if ($audienceType !== 'buildings_selected') {
                return false;
            }

            $selectedBuildings = request('buildings_selected', []);
            if (empty($selectedBuildings) || !is_array($selectedBuildings)) {
                return false;
            }

            $managedIds = $user->managedBuildings->pluck('id');
            // Check if all selected are managed
            if (collect($selectedBuildings)->diff($managedIds)->isNotEmpty()) {
                return false;
            }

            return true;
        }

        return false;
    }

    public function update(User $user, Poll $poll): bool
    {
        if ($user->hasRole('admin')) return true;

        if ($user->hasRole('board_member') && $user->community_id === $poll->community_id) {
            return true;
        }

        if ($user->hasRole('building_manager') && $poll->created_by_user_id === $user->id) {
              if (request()->has('audience_type')) {
                   if (request('audience_type') !== 'buildings_selected') return false;
                   $selectedBuildings = request('buildings_selected', []);
                   $managedIds = $user->managedBuildings->pluck('id');
                   if (collect($selectedBuildings)->diff($managedIds)->isNotEmpty()) return false;
              }
              return true;
        }

        return false;
    }

    public function delete(User $user, Poll $poll): bool
    {
        if ($user->hasRole('admin')) return true;

        if ($user->hasRole('board_member') && $user->community_id === $poll->community_id) {
            return true;
        }

        if ($user->hasRole('building_manager') && $poll->created_by_user_id === $user->id) {
            return true;
        }

        return false;
    }

    public function vote(User $user, Poll $poll): bool
    {
        // Must be in same community, poll must be active, and user hasn't voted yet
        return $user->community_id === $poll->community_id &&
               $poll->starts_at <= now() &&
               $poll->ends_at >= now() &&
               !$poll->hasUserVoted($user->id);
    }
}
