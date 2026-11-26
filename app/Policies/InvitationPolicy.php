<?php

namespace App\Policies;

use App\Models\Invitation;
use App\Models\User;

class InvitationPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isOwner();
    }

    public function create(User $user): bool
    {
        return $user->isOwner();
    }

    public function view(User $user, Invitation $invitation): bool
    {
        return $user->isOwner() && $user->id === $invitation->user_id;
    }

    public function update(User $user, Invitation $invitation): bool
    {
        return $this->view($user, $invitation);
    }

    public function delete(User $user, Invitation $invitation): bool
    {
        return $this->view($user, $invitation);
    }

    public function manageGuests(User $user, Invitation $invitation): bool
    {
        return $this->view($user, $invitation);
    }

    public function manageWishes(User $user, Invitation $invitation): bool
    {
        return $this->view($user, $invitation);
    }

    public function manageStaff(User $user, Invitation $invitation): bool
    {
        return $this->view($user, $invitation);
    }

    public function viewStats(User $user, Invitation $invitation): bool
    {
        return $this->view($user, $invitation);
    }
}
