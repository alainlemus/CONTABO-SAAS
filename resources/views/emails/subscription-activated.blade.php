<x-emails.layout title="Tu suscripción está activa" header-color="#16a34a" :recipient-email="$user->email">
    <h2>¡Hola, {{ $user->name }}!</h2>
    <p>Tu suscripción a CONTABO ha sido activada exitosamente. A partir de ahora tienes acceso completo a la plataforma.
    </p>

    <div class="info-box" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
        <p style="color: #166534;">
            ✅ <strong style="color: #14532d;">Suscripción activa</strong><br>
            Tu siguiente cobro se realizará automáticamente cada mes. Puedes gestionar tu suscripción y método de pago
            desde tu panel de facturación.
        </p>
    </div>

    <p>Con tu suscripción activa puedes:</p>
    <ul style="padding-left: 20px; margin: 0 0 20px;">
        <li style="line-height: 1.8; color: #4b5563; font-size: 15px;">Gestionar clientes y su información fiscal (RFC,
            regímenes SAT, e.firma)</li>
        <li style="line-height: 1.8; color: #4b5563; font-size: 15px;">Registrar y consultar facturas CFDI</li>
        <li style="line-height: 1.8; color: #4b5563; font-size: 15px;">Controlar obligaciones fiscales y sus vencimientos
        </li>
        <li style="line-height: 1.8; color: #4b5563; font-size: 15px;">Gestionar tu equipo de capturistas y lectores
        </li>
    </ul>

    <div class="btn-wrap">
        <a href="{{ config('app.url') }}/admin" class="btn" style="background: #16a34a; color: #ffffff;">Ir a mi
            panel →</a>
    </div>

    <hr class="divider">
    <p style="font-size: 14px;">Si tienes alguna duda sobre tu suscripción, responde a este correo y con gusto te
        ayudamos.</p>
    <p style="font-size: 14px;">— El equipo de CONTABO</p>
</x-emails.layout>
