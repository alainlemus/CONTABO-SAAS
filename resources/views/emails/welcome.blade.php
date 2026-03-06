<x-emails.layout title="Bienvenido a CONTABO" :recipient-email="$user->email">
    <h2>¡Hola, {{ $user->name }}!</h2>
    <p>Tu cuenta ha sido creada exitosamente. Estamos muy contentos de tenerte en CONTABO, la plataforma de contabilidad
        diseñada para despachos mexicanos.</p>

    <div class="info-box" style="background: #fffbeb; border: 1px solid #fde68a;">
        <p style="color: #92400e;">
            🎉 Tu <strong style="color: #78350f;">prueba gratuita de 14 días</strong> ha comenzado hoy.<br>
            Tienes acceso completo a todas las funciones hasta el <strong
                style="color: #78350f;">{{ $user->trial_ends_at->translatedFormat('d \d\e F \d\e Y') }}</strong>.
        </p>
    </div>

    <p>Con CONTABO puedes:</p>
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
        <a href="{{ config('app.url') }}/admin" class="btn" style="background: #d97706; color: #ffffff;">Ir a mi
            panel →</a>
    </div>

    <hr class="divider">
    <p style="font-size: 14px;">Si tienes alguna duda, responde a este correo y con gusto te ayudamos.</p>
    <p style="font-size: 14px;">— El equipo de CONTABO</p>
</x-emails.layout>
