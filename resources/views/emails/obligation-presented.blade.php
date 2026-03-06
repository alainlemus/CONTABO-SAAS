<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obligación fiscal presentada</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f4f4f5; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .header { background: #16a34a; padding: 32px 40px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 28px; font-weight: 700; letter-spacing: -0.5px; }
        .body { padding: 40px; color: #374151; }
        .body h2 { font-size: 22px; margin-top: 0; color: #111827; }
        .body p { line-height: 1.6; color: #4b5563; margin: 0 0 16px; }
        .success-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 20px 24px; margin: 24px 0; }
        .success-box p { margin: 0; font-size: 15px; color: #166534; }
        .success-box strong { color: #14532d; }
        table { width: 100%; border-collapse: collapse; margin: 24px 0; font-size: 14px; }
        th { background: #f9fafb; border: 1px solid #e5e7eb; padding: 10px 14px; text-align: left; font-weight: 600; color: #374151; }
        td { border: 1px solid #e5e7eb; padding: 10px 14px; color: #4b5563; vertical-align: top; }
        .badge-presented { display: inline-block; background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 9999px; font-size: 12px; font-weight: 600; }
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
            <h2>Hola, {{ $obligation->client->name }}</h2>
            <p>Tu contador ha presentado la siguiente obligación fiscal ante el SAT:</p>

            <div class="success-box">
                <p>✅ Estado: <strong>Presentada</strong></p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Detalle</th>
                        <th>Información</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Obligación</strong></td>
                        <td>{{ $obligation->type->label() }}</td>
                    </tr>
                    <tr>
                        <td><strong>Período</strong></td>
                        <td>{{ $obligation->periodLabel() }}</td>
                    </tr>
                    <tr>
                        <td><strong>Fecha de vencimiento</strong></td>
                        <td>{{ $obligation->due_date->translatedFormat('d \d\e F \d\e Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Fecha de presentación</strong></td>
                        <td>{{ $obligation->presented_at->translatedFormat('d \d\e F \d\e Y') }}</td>
                    </tr>
                    @if ($obligation->reference)
                    <tr>
                        <td><strong>Número de acuse / referencia SAT</strong></td>
                        <td>{{ $obligation->reference }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>

            <p>Si tienes alguna duda sobre esta presentación, comunícate con tu contador.</p>

            <p>— El equipo de CONTABO</p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} CONTABO. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
