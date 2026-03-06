<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'CONTABO' }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f1f5f9; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }

        /* Header */
        .header { padding: 28px 40px; text-align: center; }
        .header .logo { font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: 1px; text-transform: uppercase; }
        .header .logo span { opacity: 0.75; font-weight: 400; font-size: 13px; display: block; margin-top: 2px; letter-spacing: 0.5px; }

        /* Body */
        .body { padding: 40px; color: #374151; }
        .body h2 { font-size: 22px; margin: 0 0 12px; color: #0f172a; font-weight: 700; }
        .body p { line-height: 1.65; color: #4b5563; margin: 0 0 16px; font-size: 15px; }
        .body p:last-child { margin-bottom: 0; }

        /* Info box */
        .info-box { border-radius: 8px; padding: 20px 24px; margin: 24px 0; }
        .info-box p { margin: 0; font-size: 14px; line-height: 1.6; }

        /* Button */
        .btn-wrap { text-align: center; margin: 28px 0; }
        .btn { display: inline-block; color: #ffffff; text-decoration: none; padding: 14px 36px; border-radius: 7px; font-weight: 700; font-size: 15px; letter-spacing: 0.2px; }

        /* Divider */
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 28px 0; }

        /* Footer */
        .footer { padding: 24px 40px; background: #f8fafc; border-top: 1px solid #e2e8f0; text-align: center; }
        .footer p { margin: 0 0 4px; font-size: 12px; color: #94a3b8; line-height: 1.5; }
        .footer p:last-child { margin: 0; }
        .footer a { color: #94a3b8; text-decoration: underline; }

        /* Responsive */
        @media (max-width: 620px) {
            .wrapper { margin: 0; border-radius: 0; }
            .body, .header, .footer { padding-left: 24px; padding-right: 24px; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header" style="background: {{ $headerColor ?? '#d97706' }};">
            <div class="logo">
                <img src="{{ config('app.url') }}/images/contabo.png" alt="CONTABO" style="height: 40px; max-width: 180px; display: block; margin: 0 auto;">
            </div>
        </div>

        <div class="body">
            {{ $slot }}
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} CONTABO. Todos los derechos reservados.</p>
            <p>Este correo fue enviado a <strong>{{ $recipientEmail ?? '' }}</strong></p>
        </div>
    </div>
</body>
</html>
