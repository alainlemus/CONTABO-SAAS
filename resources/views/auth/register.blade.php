<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta — ContaboSaaS</title>
    <link rel="icon" type="image/png" href="/images/favicon.png">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        nav {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 2rem;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
        }
        .logo { font-size: 1.2rem; font-weight: 800; color: #d97706; text-decoration: none; }
        .nav-link { font-size: .875rem; color: #64748b; }
        .nav-link a { color: #d97706; font-weight: 600; }

        main {
            flex: 1;
            display: flex; align-items: center; justify-content: center;
            padding: 3rem 1rem;
        }
        .card {
            width: 100%; max-width: 460px;
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(0,0,0,.08);
            padding: 2.5rem;
        }
        .card-title { font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: .35rem; }
        .card-sub { font-size: .9rem; color: #64748b; margin-bottom: 2rem; }

        .badge-trial {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .4rem .85rem; border-radius: 99px;
            background: #dcfce7; border: 1px solid #86efac;
            font-size: .8rem; font-weight: 700; color: #15803d;
            margin-bottom: 1.5rem;
        }

        label {
            display: block; font-size: .85rem; font-weight: 600;
            color: #334155; margin-bottom: .35rem;
        }
        .field { margin-bottom: 1.25rem; }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%; padding: .7rem 1rem;
            border: 1.5px solid #e2e8f0; border-radius: .5rem;
            font-size: .95rem; color: #1e293b;
            outline: none; transition: border-color .15s;
            background: #fff;
        }
        input:focus { border-color: #d97706; box-shadow: 0 0 0 3px rgba(217,119,6,.1); }
        .error-msg { font-size: .8rem; color: #dc2626; margin-top: .3rem; }

        .alert-errors {
            background: #fef2f2; border: 1px solid #fca5a5;
            border-radius: .5rem; padding: 1rem 1.25rem;
            margin-bottom: 1.25rem;
        }
        .alert-errors ul { list-style: none; }
        .alert-errors li { font-size: .85rem; color: #dc2626; margin-bottom: .2rem; }
        .alert-errors li::before { content: '· '; }

        .btn-submit {
            width: 100%; padding: .875rem;
            background: #d97706; color: #fff;
            border: none; border-radius: .5rem;
            font-size: 1rem; font-weight: 700;
            cursor: pointer; transition: background .15s;
            margin-top: .25rem;
        }
        .btn-submit:hover { background: #b45309; }

        .terms { font-size: .78rem; color: #94a3b8; text-align: center; margin-top: 1rem; }
        .divider { text-align: center; margin: 1.5rem 0; font-size: .82rem; color: #94a3b8; }
        .login-link { text-align: center; font-size: .875rem; color: #64748b; }
        .login-link a { color: #d97706; font-weight: 600; }

        .features-mini { margin-bottom: 1.75rem; display: flex; flex-direction: column; gap: .4rem; }
        .feature-mini { font-size: .85rem; color: #475569; display: flex; align-items: center; gap: .5rem; }
        .feature-mini::before { content: '✓'; color: #16a34a; font-weight: 700; }
    </style>
</head>
<body>
<nav>
    <a href="/" class="logo"><img src="/images/contabo.png" alt="ContaboSaaS" style="height: 2rem; display: block;"></a>
    <span class="nav-link">¿Ya tienes cuenta? <a href="/admin/login">Iniciar sesión</a></span>
</nav>

<main>
    <div class="card">
        <div class="badge-trial">✓ 14 días gratis · Sin tarjeta requerida</div>
        <h1 class="card-title">Crea tu cuenta</h1>
        <p class="card-sub">Empieza a gestionar tu despacho hoy mismo.</p>

        <div class="features-mini">
            <div class="feature-mini">Clientes y obligaciones fiscales ilimitadas</div>
            <div class="feature-mini">Importación de CFDI XML automática</div>
            <div class="feature-mini">Calendario fiscal por régimen SAT</div>
        </div>

        @if ($errors->any())
            <div class="alert-errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/register">
            @csrf

            <div class="field">
                <label for="name">Nombre completo</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                       placeholder="Ej. Carlos Ramírez Contadores" autocomplete="name" required>
            </div>

            <div class="field">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       placeholder="contador@despacho.mx" autocomplete="email" required>
            </div>

            <div class="field">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password"
                       placeholder="Mínimo 8 caracteres" autocomplete="new-password" required>
            </div>

            <div class="field">
                <label for="password_confirmation">Confirmar contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       placeholder="Repite tu contraseña" autocomplete="new-password" required>
            </div>

            <button type="submit" class="btn-submit">Crear cuenta gratis</button>

            <p class="terms">Al registrarte aceptas los Términos de Servicio y la Política de Privacidad.</p>
        </form>

        <div class="divider">— o —</div>
        <p class="login-link">¿Ya tienes cuenta? <a href="/admin/login">Inicia sesión</a></p>
    </div>
</main>
</body>
</html>
