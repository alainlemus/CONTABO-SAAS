<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Client $client): bool
    {
        return $client->user_id === $user->ownerId();
    }

    public function create(User $user): bool
    {
        return $user->role->canCreate();
    }

    public function update(User $user, Client $client): bool
    {
        return $user->role->canEdit() && $client->user_id === $user->ownerId();
    }

    public function downloadCertificate(User $user, Client $client): bool
    {
        return $user->isAdmin() && $client->user_id === $user->ownerId();
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->role->canDelete() && $client->user_id === $user->ownerId();
    }

    public function restore(User $user, Client $client): bool
    {
        return $user->role->canDelete() && $client->user_id === $user->ownerId();
    }

    public function forceDelete(User $user, Client $client): bool
    {
        return $user->role->canDelete() && $client->user_id === $user->ownerId();
    }
}
