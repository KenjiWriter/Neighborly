<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'board_member', 'accountant']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Document $document): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole(['board_member', 'accountant'])) {
            return $user->community_id === $document->community_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create (upload) models.
     */
    public function upload(User $user): bool
    {
        return $user->hasRole(['admin', 'accountant']);
    }

    /**
     * Determine whether the user can download the model.
     */
    public function download(User $user, Document $document): bool
    {
        return $this->view($user, $document);
    }
}
