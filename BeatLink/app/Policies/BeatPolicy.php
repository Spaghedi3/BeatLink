<?php

namespace App\Policies;

use App\Models\Beat;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BeatPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Beat $beat)
    {
        // If the beat is public, anyone can view
        if (! $beat->is_private) {
            return true;
        }

        // If it's private, only the owner can view
        return $user && $user->id === $beat->user_id;
    }


    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Beat $beat)
    {
        return $user->id === $beat->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Beat $beat)
    {
        return $user->id === $beat->user_id;
    }

    public function delete(User $user, Beat $beat)
    {
        return $user->id === $beat->user_id;
    }


    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Beat $beat): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Beat $beat): bool
    {
        return false;
    }
}
