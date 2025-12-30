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
        // Must be in same community and announcement must be published
        if ($user->hasRole(['admin', 'board_member', 'accountant'])) {
            return $user->community_id === $announcement->community_id || $user->hasRole('admin');
        }

        // Residents can view published announcements in their community
        return $user->community_id === $announcement->community_id && $announcement->published_at !== null && $announcement->published_at <= now();
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'board_member']);
    }

    public function update(User $user, Announcement $announcement): bool
    {
        if ($user->hasRole('admin')) return true;
        
        return $user->hasRole('board_member') && 
               $user->community_id === $announcement->community_id;
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        if ($user->hasRole('admin')) return true;

        return $user->hasRole('board_member') && 
               $user->community_id === $announcement->community_id;
    }
}
