<?php

namespace App\Filament\Pages;

use App\Models\User;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class BillingPage extends Page
{
    protected static ?string $navigationLabel = 'Mi Suscripción';

    protected static string|\UnitEnum|null $navigationGroup = 'Cuenta';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.pages.billing-page';

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function getTitle(): string
    {
        return 'Mi Suscripción';
    }

    /**
     * Returns the owner user (the admin with the Stripe subscription).
     */
    protected function getOwner(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->isAdmin() ? $user : User::find($user->owner_id) ?? $user;
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
        $owner = $this->getOwner();

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
                    'cancel_url' => route('subscription.cancel'),
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

    /**
     * Redirect to the Stripe Billing Portal to manage payment methods.
     */
    public function openPortal(): mixed
    {
        $owner = $this->getOwner();

        if (! $this->isStripeConfigured()) {
            Notification::make()
                ->title('Stripe no configurado')
                ->body('Esta función no está disponible en el entorno actual.')
                ->warning()
                ->send();

            return null;
        }

        try {
            $portalUrl = $owner->billingPortalUrl(route('filament.admin.pages.billing-page'));

            return redirect($portalUrl);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Stripe billing portal error', [
                'message' => $e->getMessage(),
                'user_id' => $owner->id,
                'stripe_id' => $owner->stripe_id,
            ]);

            Notification::make()
                ->title('Error al abrir el portal')
                ->body('No se pudo acceder al portal de facturación. Por favor intenta de nuevo.')
                ->danger()
                ->send();

            return null;
        }
    }

    /**
     * Cancel the active subscription (with grace period).
     */
    public function cancelSubscription(): void
    {
        $owner = $this->getOwner();

        if (! $this->isStripeConfigured()) {
            Notification::make()
                ->title('Stripe no configurado')
                ->body('Esta función no está disponible en el entorno actual.')
                ->warning()
                ->send();

            return;
        }

        try {
            $owner->subscription('default')?->cancel();

            Notification::make()
                ->title('Suscripción cancelada')
                ->body('Tu suscripción se cancelará al final del período actual.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al cancelar')
                ->body('No se pudo cancelar la suscripción. Por favor intenta de nuevo.')
                ->danger()
                ->send();
        }
    }

    /**
     * Resume a subscription in grace period.
     */
    public function resumeSubscription(): void
    {
        $owner = $this->getOwner();

        if (! $this->isStripeConfigured()) {
            Notification::make()
                ->title('Stripe no configurado')
                ->body('Esta función no está disponible en el entorno actual.')
                ->warning()
                ->send();

            return;
        }

        try {
            $owner->subscription('default')?->resume();

            Notification::make()
                ->title('Suscripción reactivada')
                ->body('Tu suscripción ha sido reactivada exitosamente.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al reactivar')
                ->body('No se pudo reactivar la suscripción. Por favor intenta de nuevo.')
                ->danger()
                ->send();
        }
    }

    /**
     * Data passed to the view.
     *
     * @return array<string, mixed>
     */
    public function getViewData(): array
    {
        $owner = $this->getOwner();
        $isStripeConfigured = $this->isStripeConfigured();
        $subscription = $isStripeConfigured ? $owner->subscription('default') : null;

        $status = match (true) {
            $owner->onTrial() => 'trial',
            $subscription?->onGracePeriod() => 'grace_period',
            $subscription?->active() => 'active',
            $subscription?->canceled() => 'canceled',
            default => 'none',
        };

        $paymentMethod = null;
        $invoices = collect();

        if ($isStripeConfigured) {
            try {
                $paymentMethod = $owner->defaultPaymentMethod();
            } catch (\Exception) {
            }

            try {
                $invoices = $owner->invoices();
            } catch (\Exception) {
                $invoices = collect();
            }
        }

        return [
            'owner' => $owner,
            'status' => $status,
            'onTrial' => $owner->onTrial(),
            'trialEndsAt' => $owner->trialEndsAt(),
            'subscription' => $subscription,
            'paymentMethod' => $paymentMethod,
            'invoices' => $invoices,
            'isStripeConfigured' => $isStripeConfigured,
        ];
    }
}
