<x-filament-panels::page>

    <div class="flex min-h-[60vh] flex-col items-center justify-center gap-8 py-12">

        {{-- Ícono principal --}}
        <div class="flex h-24 w-24 items-center justify-center rounded-full bg-danger-100 dark:bg-danger-950">
            <x-filament::icon
                icon="heroicon-o-lock-closed"
                class="h-12 w-12 text-danger-600 dark:text-danger-400"
            />
        </div>

        {{-- Título y descripción --}}
        <div class="max-w-lg space-y-3 text-center">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Tu suscripción ha vencido
            </h2>

            @if ($this->isAdmin())
                <p class="text-base text-gray-600 dark:text-gray-400">
                    El período de tu suscripción ha terminado. Para seguir usando Contabo y permitir que tu equipo
                    continúe trabajando, renueva tu suscripción.
                </p>
            @else
                <p class="text-base text-gray-600 dark:text-gray-400">
                    La suscripción de tu cuenta ha vencido. Por el momento no puedes realizar ninguna acción.
                    Contacta al administrador de tu cuenta para renovar la suscripción.
                </p>
            @endif
        </div>

        {{-- Acción principal --}}
        @if ($this->isAdmin())
            <div class="flex flex-col items-center gap-3 sm:flex-row">
                <x-filament::button
                    tag="a"
                    href="{{ $this->getBillingUrl() }}"
                    color="primary"
                    size="lg"
                    icon="heroicon-o-credit-card"
                >
                    Renovar suscripción
                </x-filament::button>
            </div>
        @else
            <x-filament::callout
                color="warning"
                icon="heroicon-o-exclamation-triangle"
                heading="Cuenta suspendida"
                description="Contacta al administrador de tu cuenta para reactivar el acceso."
            />
        @endif

    </div>

</x-filament-panels::page>
