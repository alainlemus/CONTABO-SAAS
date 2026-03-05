<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a CONTABO</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f4f4f5; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .header { background: #f59e0b; padding: 32px 40px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 28px; font-weight: 700; letter-spacing: -0.5px; }
        .body { padding: 40px; color: #374151; }
        .body h2 { font-size: 22px; margin-top: 0; color: #111827; }
        .body p { line-height: 1.6; color: #4b5563; margin: 0 0 16px; }
        .trial-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 20px 24px; margin: 24px 0; }
        .trial-box p { margin: 0; font-size: 15px; color: #92400e; }
        .trial-box strong { color: #78350f; }
        .btn { display: inline-block; background: #f59e0b; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-weight: 600; font-size: 16px; margin: 8px 0 24px; }
        .features { margin: 24px 0; }
        .features li { line-height: 1.8; color: #4b5563; }
        .footer { padding: 24px 40px; background: #f9fafb; border-top: 1px solid #e5e7eb; text-align: center; }
        .footer p { margin: 0; font-size: 13px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>CONTABO</h1>
        </div>
        <div class="body">
            <h2>¡Hola, {{ $user->name }}!</h2>
            <p>Tu cuenta ha sido creada exitosamente. Estamos muy contentos de tenerte en CONTABO, la plataforma de contabilidad diseñada para despachos mexicanos.</p>

            <div class="trial-box">
                <p>🎉 Tu <strong>prueba gratuita de 14 días</strong> ha comenzado hoy.<br>
                Tienes acceso completo a todas las funciones hasta el <strong>{{ $user->trial_ends_at->translatedFormat('d \d\e F \d\e Y') }}</strong>.</p>
            </div>

            <p>Con CONTABO puedes:</p>
            <ul class="features">
                <li>Gestionar clientes y su información fiscal (RFC, regímenes SAT, e.firma)</li>
                <li>Registrar y consultar facturas CFDI</li>
                <li>Controlar obligaciones fiscales y sus vencimientos</li>
                <li>Gestionar tu equipo de capturistas y lectores</li>
            </ul>

            <p style="text-align:center;">
                <a href="{{ config('app.url') }}/admin" class="btn">Ir a mi panel →</a>
            </p>

            <p>Si tienes alguna duda, responde a este correo y con gusto te ayudamos.</p>
            <p>— El equipo de CONTABO</p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} CONTABO. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
