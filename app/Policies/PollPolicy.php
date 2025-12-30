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
        // Must be in same community and poll must be active
        if ($user->hasRole(['admin', 'board_member'])) {
            return $user->community_id === $poll->community_id || $user->hasRole('admin');
        }

        // Residents can view active polls in their community
        return $user->community_id === $poll->community_id && 
               $poll->starts_at <= now() && 
               $poll->ends_at >= now();
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'board_member']);
    }

    public function update(User $user, Poll $poll): bool
    {
        if ($user->hasRole('admin')) return true;

        return $user->hasRole('board_member') && 
               $user->community_id === $poll->community_id;
    }

    public function delete(User $user, Poll $poll): bool
    {
        if ($user->hasRole('admin')) return true;

        return $user->hasRole('board_member') && 
               $user->community_id === $poll->community_id;
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
