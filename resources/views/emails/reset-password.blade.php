<x-emails.layout title="Restablecer contraseña" :recipient-email="$user->email">
    <h2>Hola, {{ $user->name }}</h2>
    <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta en Contabo.</p>

    <div class="info-box" style="background: #fffbeb; border: 1px solid #fde68a;">
        <p style="color: #92400e;">
            🔐 Este enlace es válido por <strong style="color: #78350f;">60 minutos</strong>.
            Si no solicitaste el cambio, ignora este correo y tu contraseña permanecerá intacta.
        </p>
    </div>

    <div class="btn-wrap">
        <a href="{{ url(route('password.reset', ['token' => $token, 'email' => $user->email], false)) }}"
           class="btn"
           style="background: #d97706; color: #ffffff;">
            Restablecer contraseña →
        </a>
    </div>

    <hr class="divider">
    <p style="font-size: 13px; color: #6b7280;">
        Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
        <span style="word-break: break-all; color: #d97706;">
            {{ url(route('password.reset', ['token' => $token, 'email' => $user->email], false)) }}
        </span>
    </p>
    <p style="font-size: 14px;">— El equipo de Contabo</p>
</x-emails.layout>
