<x-emails.layout title="Método de pago actualizado" :recipient-email="$user->email">
    <h2>Hola, {{ $user->name }}</h2>
    <p>Tu método de pago en CONTABO ha sido actualizado exitosamente.</p>

    <div class="info-box" style="background: #fffbeb; border: 1px solid #fde68a;">
        <p style="color: #92400e;">
            💳 Tu nuevo método de pago quedará registrado para los próximos cobros de tu suscripción.
        </p>
    </div>

    <p>Si no realizaste este cambio, por favor contáctanos de inmediato respondiendo este correo para proteger tu
        cuenta.</p>

    <div class="btn-wrap">
        <a href="{{ config('app.url') }}/admin/billing" class="btn" style="background: #d97706; color: #ffffff;">Ver mi
            suscripción →</a>
    </div>

    <hr class="divider">
    <p style="font-size: 14px;">Si tienes alguna duda, responde a este correo y con gusto te ayudamos.</p>
    <p style="font-size: 14px;">— El equipo de CONTABO</p>
</x-emails.layout>
