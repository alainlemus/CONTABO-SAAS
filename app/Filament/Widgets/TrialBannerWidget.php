<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Notifications\Notification;
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

    /**
     * Detect if Stripe is configured with real keys.
     */
    protected function isStripeConfigured(): bool
    {
        $secret = config('cashier.secret');

        return $secret && ! str_contains((string) $secret, 'placeholder');
    }

    /**
     * Redirect to Stripe Checkout to start a new subscription.
     */
    public function startCheckout(): mixed
    {
        /** @var User $user */
        $user = auth()->user();
        $owner = $user->isAdmin() ? $user : User::find($user->owner_id) ?? $user;

        if (! $this->isStripeConfigured()) {
            Notification::make()
                ->title('Stripe no configurado')
                ->body('Esta función no está disponible en el entorno actual.')
                ->warning()
                ->send();

            return null;
        }

        $priceId = config('services.stripe.price_id');

        try {
            $checkout = $owner
                ->newSubscription('default', $priceId)
                ->allowPromotionCodes()
                ->checkout([
                    'success_url' => route('subscription.success').'?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('filament.admin.pages.dashboard'),
                ]);

            return redirect($checkout->url);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al iniciar el pago')
                ->body('No se pudo iniciar el proceso de suscripción. Por favor intenta de nuevo.')
                ->danger()
                ->send();

            return null;
        }
    }
}
