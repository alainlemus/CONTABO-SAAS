<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Aviso: Stripe no configurado --}}
        @if(! $isStripeConfigured)
            <div>
                <x-filament::callout
                    color="warning"
                    icon="heroicon-o-exclamation-triangle"
                    heading="Entorno de desarrollo"
                    description="Stripe no está configurado. Las funciones de suscripción no están disponibles."
                />
            </div>
        @endif

        {{-- Estado de la suscripción --}}
        <x-filament::section>
            <x-slot name="heading">Estado de la suscripción</x-slot>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    @if($status === 'trial')
                        <x-filament::badge color="warning" size="lg">
                            Período de prueba activo
                        </x-filament::badge>
                        @if($trialEndsAt)
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Tu prueba termina el <strong>{{ $trialEndsAt->format('d/m/Y') }}</strong>
                                ({{ $trialEndsAt->diffForHumans() }}).
                            </p>
                        @endif

                    @elseif($status === 'active')
                        <x-filament::badge color="success" size="lg">
                            Suscripción activa
                        </x-filament::badge>
                        @if($subscription?->ends_at)
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Próximo cobro: <strong>{{ $subscription->ends_at->format('d/m/Y') }}</strong>
                            </p>
                        @endif

                    @elseif($status === 'grace_period')
                        <x-filament::badge color="warning" size="lg">
                            Período de gracia
                        </x-filament::badge>
                        @if($subscription?->ends_at)
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Tu acceso termina el <strong>{{ $subscription->ends_at->format('d/m/Y') }}</strong>.
                                Reactiva para no perder el acceso.
                            </p>
                        @endif

                    @elseif($status === 'canceled')
                        <x-filament::badge color="danger" size="lg">
                            Cancelada
                        </x-filament::badge>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Tu suscripción fue cancelada.
                        </p>

                    @else
                        <x-filament::badge color="gray" size="lg">
                            Sin suscripción
                        </x-filament::badge>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            No tienes una suscripción activa.
                        </p>
                    @endif
                </div>

                {{-- Acciones según estado --}}
                <div class="flex flex-col gap-2 sm:flex-row">
                    @if($status === 'trial' || $status === 'none' || $status === 'canceled')
                        @if($isStripeConfigured)
                            <x-filament::button
                                wire:click="startCheckout"
                                color="primary"
                                icon="heroicon-o-credit-card"
                            >
                                Activar suscripción
                            </x-filament::button>
                        @endif

                    @elseif($status === 'active')
                        @if($isStripeConfigured)
                            <x-filament::button
                                wire:click="cancelSubscription"
                                wire:confirm="¿Seguro que deseas cancelar tu suscripción? Mantendrás el acceso hasta el final del período actual."
                                color="danger"
                                icon="heroicon-o-x-circle"
                            >
                                Cancelar suscripción
                            </x-filament::button>
                        @endif

                    @elseif($status === 'grace_period')
                        @if($isStripeConfigured)
                            <x-filament::button
                                wire:click="resumeSubscription"
                                color="success"
                                icon="heroicon-o-arrow-path"
                            >
                                Reactivar suscripción
                            </x-filament::button>
                        @endif
                    @endif
                </div>
            </div>
        </x-filament::section>

        {{-- Método de pago --}}
        @if($isStripeConfigured)
            <x-filament::section>
                <x-slot name="heading">Método de pago</x-slot>

                @if($paymentMethod)
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <x-filament::icon
                                icon="heroicon-o-credit-card"
                                class="h-8 w-8 text-gray-400"
                            />
                            <div>
                                <p class="font-medium capitalize">
                                    {{ $paymentMethod->card?->brand ?? 'Tarjeta' }}
                                    terminada en <strong>{{ $paymentMethod->card?->last4 }}</strong>
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Vence {{ $paymentMethod->card?->exp_month }}/{{ $paymentMethod->card?->exp_year }}
                                </p>
                            </div>
                        </div>

                        <x-filament::button
                            wire:click="openPortal"
                            color="gray"
                            icon="heroicon-o-pencil-square"
                        >
                            Actualizar tarjeta
                        </x-filament::button>
                    </div>
                @else
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            No hay método de pago registrado.
                        </p>
                        <x-filament::button
                            wire:click="openPortal"
                            color="gray"
                            icon="heroicon-o-plus"
                        >
                            Agregar tarjeta
                        </x-filament::button>
                    </div>
                @endif
            </x-filament::section>
        @endif

        {{-- Historial de pagos --}}
        @if($isStripeConfigured)
            <x-filament::section>
                <x-slot name="heading">Historial de pagos</x-slot>

                @if($invoices->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="pb-3 text-left font-semibold text-gray-600 dark:text-gray-300">Fecha</th>
                                    <th class="pb-3 text-left font-semibold text-gray-600 dark:text-gray-300">Monto</th>
                                    <th class="pb-3 text-left font-semibold text-gray-600 dark:text-gray-300">Estado</th>
                                    <th class="pb-3 text-right font-semibold text-gray-600 dark:text-gray-300">PDF</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach($invoices as $invoice)
                                    <tr>
                                        <td class="py-3 text-gray-700 dark:text-gray-200">
                                            {{ \Carbon\Carbon::createFromTimestamp($invoice->date()->getTimestamp())->format('d/m/Y') }}
                                        </td>
                                        <td class="py-3 text-gray-700 dark:text-gray-200">
                                            ${{ number_format($invoice->total() / 100, 2) }} MXN
                                        </td>
                                        <td class="py-3">
                                            @if($invoice->paid())
                                                <x-filament::badge color="success">Pagada</x-filament::badge>
                                            @else
                                                <x-filament::badge color="danger">Pendiente</x-filament::badge>
                                            @endif
                                        </td>
                                        <td class="py-3 text-right">
                                            <a
                                                href="{{ $invoice->invoicePdf() }}"
                                                target="_blank"
                                                class="text-primary-600 hover:underline dark:text-primary-400"
                                            >
                                                Descargar
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        No hay facturas registradas todavía.
                    </p>
                @endif
            </x-filament::section>
        @endif

    </div>
</x-filament-panels::page>
