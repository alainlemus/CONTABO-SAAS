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

        if ($this->hasAccess($owner)) {
            return $next($request);
        }

        return redirect()->route('subscription.index');
    }

    private function hasAccess(User $owner): bool
    {
        // Trial activo
        if ($owner->onTrial()) {
            return true;
        }

        // Suscripción activa (incluye grace period)
        if ($owner->subscribed('default')) {
            return true;
        }

        return false;
    }
}
