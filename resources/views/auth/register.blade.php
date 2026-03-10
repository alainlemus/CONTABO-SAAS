<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- ── SEO ─────────────────────────────────────────────────────────────── --}}
    <title>Crear cuenta gratis — Contabo | Software contable para despachos en México</title>
    <meta name="description"
        content="Crea tu cuenta gratis en ContaboSaaS y empieza a gestionar tus clientes, obligaciones fiscales SAT y facturas CFDI. 14 días de prueba sin tarjeta.">
    <meta name="robots" content="noindex, follow">
    <link rel="canonical" href="{{ url('/register') }}">
    <meta property="og:title" content="Crear cuenta — ContaboSaaS">
    <meta property="og:description"
        content="14 días gratis para gestionar tu despacho contable. Sin tarjeta requerida.">
    <meta property="og:url" content="{{ url('/register') }}">
    <meta property="og:type" content="website">

    <link rel="icon" type="image/png" href="/images/favicon.png">

    <style>
        :root {
            --amber: #d97706;
            --amber-dk: #b45309;
            --amber-lt: #fef3c7;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-500: #64748b;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
            --white: #ffffff;
            --green-100: #dcfce7;
            --green-600: #16a34a;
            --red-500: #ef4444;
            --red-50: #fef2f2;
            --red-200: #fca5a5;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--slate-50);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* ── Animations ──────────────────────────────────────────────────────── */
        @keyframes navFadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes cardEntrance {
            from {
                opacity: 0;
                transform: translateY(32px) scale(0.97);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -200% center;
            }

            100% {
                background-position: 200% center;
            }
        }

        @keyframes badgePop {
            0% {
                opacity: 0;
                transform: scale(0.85);
            }

            60% {
                transform: scale(1.04);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes inputFocus {
            0% {
                box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.3);
            }

            100% {
                box-shadow: 0 0 0 4px rgba(217, 119, 6, 0.12);
            }
        }

        @keyframes checkIn {
            from {
                opacity: 0;
                transform: translateX(-12px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* ── Nav ─────────────────────────────────────────────────────────────── */
        nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 2rem;
            background: var(--white);
            border-bottom: 1px solid var(--slate-200);
            animation: navFadeIn 0.5s ease both;
            position: relative;
            z-index: 10;
        }

        .logo {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--amber);
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .logo:hover {
            opacity: 0.8;
        }

        .nav-link {
            font-size: .875rem;
            color: var(--slate-500);
        }

        .nav-link a {
            color: var(--amber);
            font-weight: 600;
            text-decoration: none;
            position: relative;
            transition: color 0.2s;
        }

        .nav-link a::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -2px;
            width: 0;
            height: 2px;
            background: var(--amber);
            border-radius: 99px;
            transition: width 0.25s;
        }

        .nav-link a:hover::after {
            width: 100%;
        }

        /* ── Main layout ─────────────────────────────────────────────────────── */
        main {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: calc(100vh - 65px);
        }

        /* ── Left panel (value props) ────────────────────────────────────────── */
        .side-panel {
            background: linear-gradient(160deg, var(--slate-50) 0%, var(--amber-lt) 100%);
            padding: 4rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .side-panel::before,
        .side-panel::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(50px);
            pointer-events: none;
        }

        .side-panel::before {
            width: 350px;
            height: 350px;
            background: rgba(217, 119, 6, 0.1);
            top: -80px;
            right: -80px;
            animation: float 9s ease-in-out infinite;
        }

        .side-panel::after {
            width: 250px;
            height: 250px;
            background: rgba(217, 119, 6, 0.07);
            bottom: -60px;
            left: -60px;
            animation: float 12s ease-in-out infinite reverse;
        }

        .side-panel>* {
            position: relative;
            z-index: 1;
        }

        .side-title {
            font-size: clamp(1.6rem, 2.5vw, 2.25rem);
            font-weight: 800;
            color: var(--slate-900);
            line-height: 1.15;
            margin-bottom: 1rem;
            animation: fadeInUp 0.8s cubic-bezier(0.22, 1, 0.36, 1) both;
            animation-delay: 0.2s;
        }

        .side-title span {
            background: linear-gradient(90deg, var(--amber), #f59e0b, var(--amber));
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer 3s linear infinite;
        }

        .side-sub {
            font-size: .95rem;
            color: var(--slate-500);
            margin-bottom: 2.5rem;
            line-height: 1.65;
            animation: fadeInUp 0.8s cubic-bezier(0.22, 1, 0.36, 1) both;
            animation-delay: 0.3s;
        }

        .value-props {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .value-prop {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
            padding: 1.25rem;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(217, 119, 6, 0.15);
            border-radius: .75rem;
            backdrop-filter: blur(8px);
            transition: transform 0.25s, box-shadow 0.25s;
            animation: checkIn 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .value-prop:nth-child(1) {
            animation-delay: 0.35s;
        }

        .value-prop:nth-child(2) {
            animation-delay: 0.45s;
        }

        .value-prop:nth-child(3) {
            animation-delay: 0.55s;
        }

        .value-prop:nth-child(4) {
            animation-delay: 0.65s;
        }

        .value-prop:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 16px rgba(217, 119, 6, 0.1);
        }

        .value-prop-icon {
            width: 40px;
            height: 40px;
            min-width: 40px;
            border-radius: .5rem;
            background: var(--amber-lt);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .value-prop-body strong {
            display: block;
            font-size: .9rem;
            font-weight: 700;
            color: var(--slate-800);
            margin-bottom: .2rem;
        }

        .value-prop-body p {
            font-size: .82rem;
            color: var(--slate-500);
            line-height: 1.5;
        }

        /* ── Right panel (form) ──────────────────────────────────────────────── */
        .form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            background: var(--white);
        }

        .card {
            width: 100%;
            max-width: 420px;
            animation: cardEntrance 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
            animation-delay: 0.1s;
        }

        .badge-trial {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .4rem .9rem;
            border-radius: 99px;
            background: var(--green-100);
            border: 1px solid #86efac;
            font-size: .8rem;
            font-weight: 700;
            color: var(--green-600);
            margin-bottom: 1.5rem;
            animation: badgePop 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
            animation-delay: 0.4s;
        }

        .card-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--slate-900);
            margin-bottom: .35rem;
        }

        .card-sub {
            font-size: .9rem;
            color: var(--slate-500);
            margin-bottom: 2rem;
        }

        label {
            display: block;
            font-size: .85rem;
            font-weight: 600;
            color: var(--slate-700);
            margin-bottom: .4rem;
        }

        .field {
            margin-bottom: 1.1rem;
            position: relative;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .input-icon {
            position: absolute;
            left: .9rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--slate-500);
            font-size: .95rem;
            pointer-events: none;
            transition: color 0.2s;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: .75rem 1rem .75rem 2.6rem;
            border: 1.5px solid var(--slate-200);
            border-radius: .5rem;
            font-size: .95rem;
            color: var(--slate-800);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: var(--white);
        }

        input:focus {
            border-color: var(--amber);
            animation: inputFocus 0.3s ease forwards;
        }

        input:focus~.input-icon {
            color: var(--amber);
        }

        .error-msg {
            font-size: .78rem;
            color: var(--red-500);
            margin-top: .3rem;
        }

        .alert-errors {
            background: var(--red-50);
            border: 1px solid var(--red-200);
            border-radius: .5rem;
            padding: 1rem 1.25rem;
            margin-bottom: 1.25rem;
        }

        .alert-errors ul {
            list-style: none;
        }

        .alert-errors li {
            font-size: .85rem;
            color: var(--red-500);
            margin-bottom: .2rem;
        }

        .alert-errors li::before {
            content: '· ';
        }

        .btn-submit {
            width: 100%;
            padding: .9rem;
            background: var(--amber);
            color: var(--white);
            border: none;
            border-radius: .5rem;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            margin-top: .5rem;
            box-shadow: 0 2px 8px rgba(217, 119, 6, 0.25);
            position: relative;
            overflow: hidden;
        }

        .btn-submit::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
            transform: translateX(-100%);
            transition: transform 0.5s;
        }

        .btn-submit:hover {
            background: var(--amber-dk);
            box-shadow: 0 4px 16px rgba(217, 119, 6, 0.4);
            transform: translateY(-1px);
        }

        .btn-submit:hover::after {
            transform: translateX(100%);
        }

        .btn-submit:active {
            transform: scale(.98);
        }

        .terms {
            font-size: .75rem;
            color: #94a3b8;
            text-align: center;
            margin-top: .85rem;
            line-height: 1.5;
        }

        .terms a {
            color: var(--amber);
            text-decoration: underline;
            text-decoration-thickness: 1px;
        }

        .divider {
            text-align: center;
            margin: 1.5rem 0;
            font-size: .82rem;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--slate-200);
        }

        .login-link {
            text-align: center;
            font-size: .875rem;
            color: var(--slate-500);
        }

        .login-link a {
            color: var(--amber);
            font-weight: 600;
            text-decoration: none;
        }

        /* Password strength indicator */
        .password-strength {
            margin-top: .4rem;
            height: 3px;
            border-radius: 99px;
            background: var(--slate-200);
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            border-radius: 99px;
            width: 0%;
            transition: width 0.3s, background 0.3s;
        }

        /* ── Responsive ──────────────────────────────────────────────────────── */
        @media (max-width: 900px) {
            main {
                grid-template-columns: 1fr;
            }

            .side-panel {
                padding: 3rem 2rem;
                display: none;
            }

            .form-panel {
                padding: 2rem 1.25rem;
                min-height: calc(100vh - 65px);
            }
        }

        @media (max-width: 480px) {
            nav {
                padding: .875rem 1.25rem;
            }

            .card-title {
                font-size: 1.35rem;
            }

            .form-panel {
                padding: 1.75rem 1rem;
                align-items: flex-start;
                padding-top: 2rem;
            }
        }
    </style>
</head>

<body>
    <nav>
        <a href="/" class="logo">
            <img src="/images/contabo.png" alt="ContaboSaaS — Inicio" style="height: 3.5rem; display: block;">
        </a>
        <span class="nav-link">¿Ya tienes cuenta? <a href="/admin/login">Iniciar sesión</a></span>
    </nav>

    <main>
        <!-- ── Panel izquierdo con propuesta de valor ─────────────────────────── -->
        <aside class="side-panel" aria-label="Beneficios de ContaboSaaS">
            <h1 class="side-title">Tu despacho, <span>organizado</span> desde hoy</h1>
            <p class="side-sub">
                Únete a los contadores mexicanos que ya automatizaron sus obligaciones SAT,
                gestionan sus CFDIs y llevan el control de su cartera desde un solo panel.
            </p>

            <div class="value-props" role="list">
                <div class="value-prop" role="listitem">
                    <div class="value-prop-icon" aria-hidden="true">📅</div>
                    <div class="value-prop-body">
                        <strong>Calendario fiscal automático</strong>
                        <p>Genera las obligaciones ISR, IVA, DIOT según el régimen SAT de cada cliente. Con fechas
                            límite correctas.</p>
                    </div>
                </div>
                <div class="value-prop" role="listitem">
                    <div class="value-prop-icon" aria-hidden="true">🧾</div>
                    <div class="value-prop-body">
                        <strong>Importación de CFDIs XML</strong>
                        <p>Sube el XML y el sistema extrae UUID, RFC, montos e impuestos automáticamente.</p>
                    </div>
                </div>
                <div class="value-prop" role="listitem">
                    <div class="value-prop-icon" aria-hidden="true">👥</div>
                    <div class="value-prop-body">
                        <strong>Trabaja con tu equipo</strong>
                        <p>Capturistas y asistentes con roles diferenciados. Tú decides quién puede ver y editar qué.
                        </p>
                    </div>
                </div>
                <div class="value-prop" role="listitem">
                    <div class="value-prop-icon" aria-hidden="true">🔒</div>
                    <div class="value-prop-body">
                        <strong>Datos seguros</strong>
                        <p>e.firma, acuses y documentos almacenados con acceso restringido por cliente y rol.</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- ── Panel derecho con formulario ──────────────────────────────────── -->
        <section class="form-panel" aria-label="Formulario de registro">
            <div class="card">
                <div class="badge-trial" role="note">✓ 14 días gratis · Sin tarjeta requerida</div>
                <h2 class="card-title">Crea tu cuenta</h2>
                <p class="card-sub">Empieza a gestionar tu despacho hoy mismo.</p>

                @if ($errors->any())
                    <div class="alert-errors" role="alert" aria-live="polite">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="/register" novalidate>
                    @csrf

                    <div class="field">
                        <label for="name">Nombre completo</label>
                        <div class="input-wrap">
                            <span class="input-icon" aria-hidden="true">👤</span>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                placeholder="Ej. Carlos Ramírez Contadores" autocomplete="name" required
                                aria-required="true" aria-describedby="{{ $errors->has('name') ? 'name-error' : '' }}">
                        </div>
                        @error('name')
                            <p class="error-msg" id="name-error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="email">Correo electrónico</label>
                        <div class="input-wrap">
                            <span class="input-icon" aria-hidden="true">✉️</span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                placeholder="contador@despacho.mx" autocomplete="email" required aria-required="true"
                                aria-describedby="{{ $errors->has('email') ? 'email-error' : '' }}">
                        </div>
                        @error('email')
                            <p class="error-msg" id="email-error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password">Contraseña</label>
                        <div class="input-wrap">
                            <span class="input-icon" aria-hidden="true">🔑</span>
                            <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres"
                                autocomplete="new-password" required aria-required="true"
                                aria-describedby="password-strength-hint {{ $errors->has('password') ? 'password-error' : '' }}"
                                oninput="updateStrength(this.value)">
                        </div>
                        <div class="password-strength" aria-hidden="true">
                            <div class="password-strength-bar" id="strength-bar"></div>
                        </div>
                        <p id="password-strength-hint" class="error-msg"
                            style="color: var(--slate-500); margin-top:.3rem;"></p>
                        @error('password')
                            <p class="error-msg" id="password-error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Confirmar contraseña</label>
                        <div class="input-wrap">
                            <span class="input-icon" aria-hidden="true">🔑</span>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                placeholder="Repite tu contraseña" autocomplete="new-password" required
                                aria-required="true">
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        Crear cuenta gratis →
                    </button>

                    <p class="terms">
                        Al registrarte aceptas los
                        <a href="{{ route('legal.terms') }}" target="_blank" rel="noopener">Términos y
                            Condiciones</a>
                        y el
                        <a href="{{ route('legal.privacy') }}" target="_blank" rel="noopener">Aviso de
                            Privacidad</a>.
                    </p>
                </form>

                <div class="divider">o</div>
                <p class="login-link">¿Ya tienes cuenta? <a href="/admin/login">Inicia sesión</a></p>
            </div>
        </section>
    </main>

    <script>
        function updateStrength(value) {
            const bar = document.getElementById('strength-bar');
            const hint = document.getElementById('password-strength-hint');
            const len = value.length;

            let score = 0;
            if (len >= 8) score++;
            if (len >= 12) score++;
            if (/[A-Z]/.test(value)) score++;
            if (/[0-9]/.test(value)) score++;
            if (/[^A-Za-z0-9]/.test(value)) score++;

            const levels = [{
                    pct: '0%',
                    color: 'transparent',
                    label: ''
                },
                {
                    pct: '25%',
                    color: '#ef4444',
                    label: 'Muy débil'
                },
                {
                    pct: '50%',
                    color: '#f59e0b',
                    label: 'Débil'
                },
                {
                    pct: '75%',
                    color: '#eab308',
                    label: 'Aceptable'
                },
                {
                    pct: '90%',
                    color: '#22c55e',
                    label: 'Fuerte'
                },
                {
                    pct: '100%',
                    color: '#16a34a',
                    label: 'Muy fuerte'
                },
            ];

            const level = levels[Math.min(score, 5)];
            bar.style.width = len > 0 ? level.pct : '0%';
            bar.style.background = level.color;
            hint.textContent = len > 0 ? level.label : '';
        }
    </script>
</body>

</html>
