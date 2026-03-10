<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscribed
{
    /**
     * Bloquea el acceso al panel si el admin dueño de la cuenta
     * no tiene suscripción activa ni está en período de trial.
     *
     * Capturistas y viewers heredan el estado de su admin dueño.
     *
     * Si la suscripción expiró completamente (post-grace-period):
     * - Se permite la entrada al panel pero se redirige a la página de expiración,
     *   excepto si ya están en esa página o en BillingPage.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        /** @var User $user */
        $user = auth()->user();

        // Resolvemos al admin dueño de la cuenta
        $owner = $user->isAdmin()
            ? $user
            : User::find($user->owner_id);

        if ($owner === null) {
            return $next($request);
        }

        // Acceso activo: trial o suscripción vigente (incluye grace period) → pasa normal
        if ($owner->onTrial() || $owner->subscribed('default')) {
            return $next($request);
        }

        // Sin ningún vínculo con la plataforma → suscripción pública
        if ($owner->trial_ends_at === null && $owner->stripe_id === null) {
            return redirect()->route('subscription.index');
        }

        // Cuenta expirada: puede entrar al panel pero solo a páginas permitidas
        $allowedPaths = [
            '/admin/subscription-expired-page',
            '/admin/billing-page',
        ];

        foreach ($allowedPaths as $allowed) {
            if (str_starts_with($request->getPathInfo(), $allowed)) {
                return $next($request);
            }
        }

        return redirect('/admin/subscription-expired-page');
    }
}
