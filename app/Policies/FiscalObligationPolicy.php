<?php

namespace App\Policies;

use App\Models\FiscalObligation;
use App\Models\User;

class FiscalObligationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, FiscalObligation $fiscalObligation): bool
    {
        return $fiscalObligation->client?->user_id === $user->ownerId();
    }

    public function create(User $user): bool
    {
        return $user->role->canCreate();
    }

    public function update(User $user, FiscalObligation $fiscalObligation): bool
    {
        return $user->role->canEdit() && $fiscalObligation->client?->user_id === $user->ownerId();
    }

    public function delete(User $user, FiscalObligation $fiscalObligation): bool
    {
        return $user->role->canDelete() && $fiscalObligation->client?->user_id === $user->ownerId();
    }

    public function restore(User $user, FiscalObligation $fiscalObligation): bool
    {
        return $user->role->canDelete() && $fiscalObligation->client?->user_id === $user->ownerId();
    }

    public function forceDelete(User $user, FiscalObligation $fiscalObligation): bool
    {
        return $user->role->canDelete() && $fiscalObligation->client?->user_id === $user->ownerId();
    }
}
