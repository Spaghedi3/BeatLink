<?php

namespace App\Policies;

use App\Models\Track;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TrackPolicy
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
    public function view(User $user, Track $track)
    {
        // If the track is public, anyone can view
        if (! $track->is_private) {
            return true;
        }

        // If it's private, only the owner can view
        return $user && $user->id === $track->user_id;
    }


    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Track $track)
    {
        return $user->id === $track->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Track $track)
    {
        return $user->id === $track->user_id;
    }

    public function delete(User $user, Track $track)
    {
        return $user->id === $track->user_id;
    }


    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Track $track): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Track $track): bool
    {
        return false;
    }
}
