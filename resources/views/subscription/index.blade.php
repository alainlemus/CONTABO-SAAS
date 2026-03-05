<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suscripción — ContaboSaaS</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e293b;
        }
        .container {
            max-width: 480px;
            width: 100%;
            padding: 2rem;
        }
        .card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(0,0,0,.08);
            padding: 2.5rem;
            text-align: center;
        }
        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: #d97706;
            margin-bottom: 1.5rem;
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: .5rem;
        }
        .subtitle {
            color: #64748b;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .alert {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: .5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: .9rem;
            color: #92400e;
        }
        .features {
            text-align: left;
            margin-bottom: 2rem;
        }
        .feature {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            margin-bottom: .75rem;
            font-size: .95rem;
        }
        .check {
            color: #16a34a;
            font-weight: 700;
            flex-shrink: 0;
            margin-top: .1rem;
        }
        .btn {
            display: block;
            width: 100%;
            padding: .875rem 1.5rem;
            border-radius: .5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
        }
        .btn-primary {
            background: #d97706;
            color: #fff;
            margin-bottom: .75rem;
        }
        .btn-primary:hover { background: #b45309; }
        .btn-secondary {
            background: transparent;
            color: #64748b;
            border: 1px solid #e2e8f0;
            font-size: .875rem;
        }
        .btn-secondary:hover { background: #f1f5f9; }
        .price {
            font-size: 2.25rem;
            font-weight: 800;
            color: #1e293b;
        }
        .price-period {
            color: #64748b;
            font-size: .9rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">ContaboSaaS</div>

            @if(! $onTrial && ! $subscribed)
                <h1>Tu período de prueba ha terminado</h1>
                <p class="subtitle">
                    Para continuar usando ContaboSaaS, activa tu suscripción.<br>
                    Primeros <strong>14 días gratis</strong>, sin tarjeta requerida para el trial.
                </p>
            @else
                <h1>Activa tu suscripción</h1>
                <p class="subtitle">Accede a todas las funciones del sistema.</p>
            @endif

            @if($onTrial && $trialEndsAt)
                <div class="alert">
                    Tu período de prueba termina el <strong>{{ $trialEndsAt->format('d/m/Y') }}</strong>.
                    Suscríbete ahora para no perder el acceso.
                </div>
            @endif

            <div class="price">$399 <span style="font-size:1rem;font-weight:400">MXN</span></div>
            <div class="price-period">por mes + IVA</div>

            <div class="features">
                <div class="feature">
                    <span class="check">✓</span>
                    <span>Clientes y expedientes ilimitados</span>
                </div>
                <div class="feature">
                    <span class="check">✓</span>
                    <span>Gestión de facturas y CFDI</span>
                </div>
                <div class="feature">
                    <span class="check">✓</span>
                    <span>Obligaciones fiscales con calendario</span>
                </div>
                <div class="feature">
                    <span class="check">✓</span>
                    <span>Dashboard con reportes</span>
                </div>
                <div class="feature">
                    <span class="check">✓</span>
                    <span>Equipo de capturistas y viewers</span>
                </div>
            </div>

            <form method="POST" action="{{ route('subscription.checkout') }}">
                @csrf
                <button type="submit" class="btn btn-primary">
                    Activar suscripción con Stripe
                </button>
            </form>

            @auth
                <a href="/admin/logout" class="btn btn-secondary" style="margin-top:.75rem">
                    Cerrar sesión
                </a>
            @endauth
        </div>
    </div>
</body>
</html>
