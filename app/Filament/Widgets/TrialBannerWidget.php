<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\Widget;

class TrialBannerWidget extends Widget
{
    protected string $view = 'filament.widgets.trial-banner';

    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        /** @var User $user */
        $user = auth()->user();
        $owner = $user->isAdmin() ? $user : User::find($user->owner_id);

        return $owner?->onTrial() && ! $owner?->subscribed('default');
    }

    public function getDaysLeft(): int
    {
        /** @var User $user */
        $user = auth()->user();
        $owner = $user->isAdmin() ? $user : User::find($user->owner_id);

        return (int) now()->diffInDays($owner?->trial_ends_at, false);
    }

    public function getTrialEndsAt(): string
    {
        /** @var User $user */
        $user = auth()->user();
        $owner = $user->isAdmin() ? $user : User::find($user->owner_id);

        return $owner?->trial_ends_at?->translatedFormat('d \d\e F \d\e Y') ?? '';
    }
}
