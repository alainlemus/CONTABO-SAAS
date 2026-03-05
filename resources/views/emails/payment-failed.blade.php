<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Problema con tu pago</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f4f4f5; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .header { background: #ef4444; padding: 32px 40px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 28px; font-weight: 700; letter-spacing: -0.5px; }
        .body { padding: 40px; color: #374151; }
        .body h2 { font-size: 22px; margin-top: 0; color: #111827; }
        .body p { line-height: 1.6; color: #4b5563; margin: 0 0 16px; }
        .alert-box { background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 20px 24px; margin: 24px 0; }
        .alert-box p { margin: 0; font-size: 15px; color: #991b1b; }
        .btn { display: inline-block; background: #ef4444; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-weight: 600; font-size: 16px; margin: 8px 0 24px; }
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
            <h2>Hola, {{ $user->name }}</h2>
            <p>Tuvimos un problema al procesar el pago de tu suscripción a CONTABO.</p>

            <div class="alert-box">
                <p>❌ El cobro a tu método de pago registrado <strong>no pudo completarse</strong>.<br>
                Para evitar interrupciones en tu servicio, actualiza tu método de pago a la brevedad.</p>
            </div>

            <p>Puedes actualizar tu método de pago desde el portal de facturación:</p>

            <p style="text-align:center;">
                <a href="{{ config('app.url') }}/subscription/portal" class="btn">Actualizar método de pago →</a>
            </p>

            <p>Si crees que esto es un error o necesitas ayuda, responde a este correo.</p>
            <p>— El equipo de CONTABO</p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} CONTABO. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
