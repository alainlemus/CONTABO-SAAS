<?php

namespace App\Filament\Pages;

use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;

class SubscriptionExpiredPage extends Page
{
    protected static ?string $navigationLabel = 'Suscripción';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static string|BackedEnum|null $navigationIcon = null;

    protected static bool $shouldRegisterNavigation = false;

    protected static string $routePath = 'subscription-expired';

    protected string $view = 'filament.pages.subscription-expired-page';

    /**
     * Solo accesible para usuarios cuya cuenta ha expirado (y que están activos).
     * Si tienen acceso activo, son redirigidos al panel normal.
     */
    public static function canAccess(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        /** @var User $user */
        $user = auth()->user();

        return $user->is_active && ! $user->hasActiveAccess();
    }

    public function getTitle(): string
    {
        return 'Suscripción vencida';
    }

    /**
     * Indica si el usuario actual es admin (puede renovar) o team member (debe contactar al admin).
     */
    public function isAdmin(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->isAdmin();
    }

    /**
     * URL de la página de facturación para renovar.
     */
    public function getBillingUrl(): string
    {
        return BillingPage::getUrl();
    }
}
