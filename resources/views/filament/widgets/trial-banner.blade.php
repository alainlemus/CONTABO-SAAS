@php
    $daysLeft = $this->getDaysLeft();
    $trialEndsAt = $this->getTrialEndsAt();
    $heading = $daysLeft === 0
        ? 'Tu período de prueba vence hoy'
        : "Te quedan {$daysLeft} " . ($daysLeft === 1 ? 'día' : 'días') . ' de prueba gratuita';
@endphp

<x-filament::callout
    :heading="$heading"
    :description="'Tu acceso de prueba vence el ' . $trialEndsAt . '. Activa tu suscripción para conservar el acceso completo sin interrupciones.'"
    color="warning"
    icon="heroicon-o-clock"
>
    <x-slot name="footer">
        <x-filament::button
            wire:click="startCheckout"
            color="warning"
            size="sm"
        >
            Activar suscripción
        </x-filament::button>
    </x-slot>
</x-filament::callout>
