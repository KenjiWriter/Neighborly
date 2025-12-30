<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        // All authenticated users can view announcements list
        return true;
    }

    public function view(User $user, Announcement $announcement): bool
    {
        // Creator can always view
        if ($announcement->created_by_user_id === $user->id) {
            return true;
        }

        // Must be in same community and announcement must be published
        if ($user->hasRole(['admin', 'board_member', 'accountant'])) {
            return $user->community_id === $announcement->community_id || $user->hasRole('admin');
        }

        // Building Manager: View if targeted at their managed buildings
        if ($user->hasRole('building_manager')) {
             if ($announcement->community_id !== $user->community_id) return false;
             if ($announcement->published_at === null || $announcement->published_at > now()) return false;
             
             // Check visibility intersection
             return $announcement->targetedBuildings()->whereIn('announcement_building_visibility.building_id', $user->managedBuildings->pluck('id'))->exists();
        }

        // Residents can view published announcements in their community
        return $user->community_id === $announcement->community_id && $announcement->published_at !== null && $announcement->published_at <= now();
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

    public function update(User $user, Announcement $announcement): bool
    {
        if ($user->hasRole('admin')) return true;
        
        if ($user->hasRole('board_member') && $user->community_id === $announcement->community_id) {
            return true;
        }

        // Building manager can update own announcements, subject to strict scoping (re-checked in create logic validation or here)
        // For simplicity, allow update if creator. Controller validation/frontend constraints help too.
        // But if they change audience to "community_all", this policy assumes request checks happen or we trust creator won't break it?
        // Better to re-enforce strict check if we can, but 'update' method doesn't always have new data on the model yet.
        // We defer data validation to FormRequest/Controller? 
        // But the Prompt said "allow ONLY IF..." for CREATE.
        // For Update, implied same rules.
        if ($user->hasRole('building_manager') && $announcement->created_by_user_id === $user->id) {
              // If request has audience data, validate it
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

    public function delete(User $user, Announcement $announcement): bool
    {
        if ($user->hasRole('admin')) return true;

        if ($user->hasRole('board_member') && $user->community_id === $announcement->community_id) {
            return true;
        }

        if ($user->hasRole('building_manager') && $announcement->created_by_user_id === $user->id) {
            return true;
        }

        return false;
    }
}
