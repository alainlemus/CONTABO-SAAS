<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $invoice->client->user_id === $user->ownerId();
    }

    public function create(User $user): bool
    {
        return $user->role->canCreate();
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->role->canEdit() && $invoice->client->user_id === $user->ownerId();
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->role->canDelete() && $invoice->client->user_id === $user->ownerId();
    }

    public function restore(User $user, Invoice $invoice): bool
    {
        return $user->role->canDelete() && $invoice->client->user_id === $user->ownerId();
    }

    public function forceDelete(User $user, Invoice $invoice): bool
    {
        return $user->role->canDelete() && $invoice->client->user_id === $user->ownerId();
    }
}
