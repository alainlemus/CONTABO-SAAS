<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /** Página principal de suscripción — muestra el plan y el botón de checkout. */
    public function index(): View|RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $owner = $user->isAdmin() ? $user : User::find($user->owner_id);

        // Si ya tiene acceso, redirigir al panel
        if ($owner && ($owner->onTrial() || $owner->subscribed('default'))) {
            return redirect('/admin');
        }

        return view('subscription.index', [
            'onTrial' => $owner?->onTrial() ?? false,
            'trialEndsAt' => $owner?->trialEndsAt(),
            'subscribed' => $owner?->subscribed('default') ?? false,
        ]);
    }

    /** Inicia una sesión de Stripe Checkout y redirige al usuario. */
    public function checkout(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $owner = $user->isAdmin() ? $user : User::find($user->owner_id);

        if (! $owner) {
            return redirect()->route('subscription.index');
        }

        // Detectar llaves placeholder (entorno sin Stripe real configurado)
        $stripeSecret = config('cashier.secret');
        if (! $stripeSecret || str_contains((string) $stripeSecret, 'placeholder')) {
            return redirect()->route('subscription.index')
                ->with('error', 'Stripe no está configurado en este entorno. Contacta al administrador.');
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
            return redirect()->route('subscription.index')
                ->with('error', 'No se pudo iniciar el proceso de suscripción. Por favor intenta de nuevo o contacta al soporte.');
        }
    }

    /** Página de éxito post-checkout. */
    public function success(): View
    {
        return view('subscription.success');
    }

    /** Página de cancelación de checkout. */
    public function cancel(): View
    {
        return view('subscription.cancel');
    }

    /** Redirige al portal de Stripe para gestionar la suscripción. */
    public function portal(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $owner = $user->isAdmin() ? $user : User::find($user->owner_id);

        if (! $owner) {
            return redirect()->route('subscription.index');
        }

        try {
            return $owner->redirectToBillingPortal(route('subscription.index'));
        } catch (\Exception $e) {
            return redirect()->route('subscription.index')
                ->with('error', 'No se pudo acceder al portal de facturación. Por favor intenta de nuevo.');
        }
    }
}
