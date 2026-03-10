<x-filament-panels::page>

    {{-- Aviso: Stripe no configurado --}}
    @if (!$isStripeConfigured)
        <x-filament::callout color="warning" icon="heroicon-o-exclamation-triangle" heading="Entorno de desarrollo"
            description="Stripe no está configurado. Las funciones de suscripción no están disponibles." />
    @endif

    {{-- Estado de la suscripción --}}
    <x-filament::section>
        <x-slot name="heading">Estado de la suscripción</x-slot>

        <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
            <div class="space-y-1 py-1">
                @if ($status === 'trial')
                    <x-filament::badge color="warning" size="lg">
                        Período de prueba activo
                    </x-filament::badge>
                    @if ($trialEndsAt)
                        <p class="text-sm text-gray-500 dark:text-gray-400"
                            style="margin-top: 0.75rem; margin-bottom: 0.75rem;">
                            Tu prueba termina el <strong>{{ $trialEndsAt->format('d/m/Y') }}</strong>
                            ({{ $trialEndsAt->diffForHumans() }}).
                        </p>
                    @endif
                @elseif ($status === 'active')
                    <x-filament::badge color="success" size="lg">
                        Suscripción activa
                    </x-filament::badge>
                    @if ($subscription?->ends_at)
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Próximo cobro: <strong>{{ $subscription->ends_at->format('d/m/Y') }}</strong>
                        </p>
                    @endif
                @elseif ($status === 'grace_period')
                    <x-filament::badge color="warning" size="lg">
                        Período de gracia
                    </x-filament::badge>
                    @if ($subscription?->ends_at)
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Tu acceso termina el <strong>{{ $subscription->ends_at->format('d/m/Y') }}</strong>.
                            Reactiva para no perder el acceso.
                        </p>
                    @endif
                @elseif ($status === 'canceled')
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
                @if ($status === 'trial' || $status === 'none' || $status === 'canceled')
                    @if ($isStripeConfigured)
                        <x-filament::button wire:click="startCheckout" color="primary" icon="heroicon-o-credit-card">
                            Activar suscripción
                        </x-filament::button>
                    @endif
                @elseif ($status === 'active')
                    @if ($isStripeConfigured)
                        <x-filament::button wire:click="cancelSubscription"
                            wire:confirm="¿Seguro que deseas cancelar tu suscripción? Mantendrás el acceso hasta el final del período actual."
                            color="danger" icon="heroicon-o-x-circle">
                            Cancelar suscripción
                        </x-filament::button>
                    @endif
                @elseif ($status === 'grace_period')
                    @if ($isStripeConfigured)
                        <x-filament::button wire:click="resumeSubscription" color="success"
                            icon="heroicon-o-arrow-path">
                            Reactivar suscripción
                        </x-filament::button>
                    @endif
                @endif
            </div>
        </div>
    </x-filament::section>

    {{-- Método de pago --}}
    @if ($isStripeConfigured && in_array($status, ['active', 'grace_period']))
        <x-filament::section>
            <x-slot name="heading">Método de pago</x-slot>

            @if ($paymentMethods->isNotEmpty())
                <div class="-mx-6 -mb-6 overflow-hidden">
                    <table
                        class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                        <thead class="divide-y divide-gray-200 dark:divide-white/5">
                            <tr>
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                    <span class="group flex w-full items-center gap-x-1">
                                        <span
                                            class="fi-ta-col-header-label text-sm font-semibold text-gray-950 dark:text-white">
                                            Tarjeta
                                        </span>
                                    </span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                    <span class="group flex w-full items-center gap-x-1">
                                        <span
                                            class="fi-ta-col-header-label text-sm font-semibold text-gray-950 dark:text-white">
                                            Vencimiento
                                        </span>
                                    </span>
                                </th>
                                <th
                                    class="fi-ta-header-cell px-3 py-3.5 text-end sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                    <span class="group flex w-full items-center justify-end gap-x-1">
                                        <span
                                            class="fi-ta-col-header-label text-sm font-semibold text-gray-950 dark:text-white">
                                            Acciones
                                        </span>
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                            @foreach ($paymentMethods as $pm)
                                <tr class="fi-ta-row">
                                    <td
                                        class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                        <div class="fi-ta-col-wrp">
                                            <div class="flex w-full items-center gap-3 px-3 py-4">
                                                <x-filament::icon icon="heroicon-o-credit-card"
                                                    class="h-5 w-5 shrink-0 text-gray-400 dark:text-gray-500" />
                                                <span
                                                    class="text-sm font-medium capitalize text-gray-950 dark:text-white">
                                                    {{ $pm->card?->brand ?? 'Tarjeta' }}
                                                    ···· {{ $pm->card?->last4 }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                        <div class="fi-ta-col-wrp">
                                            <div class="px-3 py-4">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ str_pad($pm->card?->exp_month, 2, '0', STR_PAD_LEFT) }}/{{ $pm->card?->exp_year }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                        <div class="fi-ta-col-wrp">
                                            <div class="flex items-center justify-end px-3 py-4">
                                                <x-filament::button wire:click="openPortal" color="gray"
                                                    size="sm" icon="heroicon-o-pencil-square">
                                                    Administrar
                                                </x-filament::button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        No hay método de pago registrado.
                    </p>
                    <x-filament::button wire:click="openPortal" color="gray" icon="heroicon-o-plus">
                        Agregar tarjeta
                    </x-filament::button>
                </div>
            @endif
        </x-filament::section>
    @endif

    {{-- Historial de pagos --}}
    @if ($isStripeConfigured && in_array($status, ['active', 'grace_period']))
        <x-filament::section>
            <x-slot name="heading">Historial de pagos</x-slot>

            @if ($invoices->isNotEmpty())
                <div class="-mx-6 -mb-6 overflow-hidden">
                    <table
                        class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                        <thead class="divide-y divide-gray-200 dark:divide-white/5">
                            <tr>
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                    <span class="group flex w-full items-center gap-x-1">
                                        <span
                                            class="fi-ta-col-header-label text-sm font-semibold text-gray-950 dark:text-white">
                                            Fecha
                                        </span>
                                    </span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                    <span class="group flex w-full items-center gap-x-1">
                                        <span
                                            class="fi-ta-col-header-label text-sm font-semibold text-gray-950 dark:text-white">
                                            Monto
                                        </span>
                                    </span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                    <span class="group flex w-full items-center gap-x-1">
                                        <span
                                            class="fi-ta-col-header-label text-sm font-semibold text-gray-950 dark:text-white">
                                            Estado
                                        </span>
                                    </span>
                                </th>
                                <th
                                    class="fi-ta-header-cell px-3 py-3.5 text-end sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                    <span class="group flex w-full items-center justify-end gap-x-1">
                                        <span
                                            class="fi-ta-col-header-label text-sm font-semibold text-gray-950 dark:text-white">
                                            PDF
                                        </span>
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                            @foreach ($invoices as $invoice)
                                <tr class="fi-ta-row">
                                    <td
                                        class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                        <div class="fi-ta-col-wrp">
                                            <div class="px-3 py-4">
                                                <span class="text-sm text-gray-950 dark:text-white">
                                                    {{ \Carbon\Carbon::createFromTimestamp($invoice->date()->getTimestamp())->format('d/m/Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                        <div class="fi-ta-col-wrp">
                                            <div class="px-3 py-4">
                                                <span class="text-sm font-medium text-gray-950 dark:text-white">
                                                    ${{ number_format($invoice->rawTotal() / 100, 2) }} MXN
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                        <div class="fi-ta-col-wrp">
                                            <div class="px-3 py-4">
                                                @if ($invoice->isPaid())
                                                    <x-filament::badge color="success">Pagada</x-filament::badge>
                                                @else
                                                    <x-filament::badge color="danger">Pendiente</x-filament::badge>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                        <div class="fi-ta-col-wrp">
                                            <div class="flex items-center justify-end px-3 py-4">
                                                @if ($invoice->invoice_pdf)
                                                    <x-filament::link href="{{ $invoice->invoice_pdf }}"
                                                        target="_blank" icon="heroicon-o-arrow-down-tray"
                                                        size="sm">
                                                        Descargar
                                                    </x-filament::link>
                                                @elseif ($invoice->hosted_invoice_url)
                                                    <x-filament::link href="{{ $invoice->hosted_invoice_url }}"
                                                        target="_blank" icon="heroicon-o-arrow-top-right-on-square"
                                                        size="sm">
                                                        Ver factura
                                                    </x-filament::link>
                                                @else
                                                    <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
                                                @endif
                                            </div>
                                        </div>
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

</x-filament-panels::page>
