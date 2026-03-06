<x-emails.layout title="Problema con tu pago" header-color="#dc2626" :recipient-email="$user->email">
    <h2>Hola, {{ $user->name }}</h2>
    <p>Tuvimos un problema al procesar el pago de tu suscripción a CONTABO.</p>

    <div class="info-box" style="background: #fef2f2; border: 1px solid #fecaca;">
        <p style="color: #991b1b;">
            ❌ El cobro a tu método de pago registrado <strong style="color: #7f1d1d;">no pudo completarse</strong>.<br>
            Para evitar interrupciones en tu servicio, actualiza tu método de pago a la brevedad.
        </p>
    </div>

    <p>Puedes actualizar tu método de pago desde el portal de facturación:</p>

    <div class="btn-wrap">
        <a href="{{ config('app.url') }}/subscription/portal" class="btn"
            style="background: #dc2626; color: #ffffff;">Actualizar método de pago →</a>
    </div>

    <hr class="divider">
    <p style="font-size: 14px;">Si crees que esto es un error o necesitas ayuda, responde a este correo.</p>
    <p style="font-size: 14px;">— El equipo de CONTABO</p>
</x-emails.layout>
