<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¿Olvidaste tu contraseña? — Contabo</title>
    <meta name="description" content="Recupera el acceso a tu cuenta de Contabo. Te enviaremos un enlace seguro a tu correo.">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="/images/favicon.png">

    <style>
        :root {
            --amber:    #d97706;
            --amber-dk: #b45309;
            --amber-lt: #fef3c7;
            --slate-50:  #f8fafc;
            --slate-200: #e2e8f0;
            --slate-500: #64748b;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
            --white: #ffffff;
            --green-50:  #f0fdf4;
            --green-700: #15803d;
            --green-200: #bbf7d0;
            --red-50:    #fef2f2;
            --red-500:   #ef4444;
            --red-200:   #fca5a5;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--slate-50);
            min-height: 100vh;
        }

        /* ── Animations ── */
        @keyframes navFadeIn  { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInUp   { from { opacity: 0; transform: translateY(24px); }  to { opacity: 1; transform: translateY(0); } }
        @keyframes cardEntrance { from { opacity: 0; transform: translateY(32px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes float      { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
        @keyframes shimmer    { 0% { background-position: -200% center; } 100% { background-position: 200% center; } }
        @keyframes checkIn    { from { opacity: 0; transform: translateX(-12px); } to { opacity: 1; transform: translateX(0); } }

        /* ── Nav ── */
        .fp-nav {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 2rem;
            background: var(--white);
            border-bottom: 1px solid var(--slate-200);
            animation: navFadeIn 0.5s ease both;
            position: relative; z-index: 10;
        }
        .fp-logo { text-decoration: none; transition: opacity 0.2s; }
        .fp-logo:hover { opacity: 0.8; }
        .fp-nav-link { font-size: .875rem; color: var(--slate-500); }
        .fp-nav-link a {
            color: var(--amber); font-weight: 600; text-decoration: none; position: relative;
        }
        .fp-nav-link a::after {
            content: ''; position: absolute; left: 0; bottom: -2px;
            width: 0; height: 2px; background: var(--amber);
            border-radius: 99px; transition: width 0.25s;
        }
        .fp-nav-link a:hover::after { width: 100%; }

        /* ── Two-column layout ── */
        .fp-main {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: calc(100vh - 65px);
        }

        /* ── Left panel ── */
        .fp-side {
            background: linear-gradient(160deg, var(--slate-50) 0%, var(--amber-lt) 100%);
            padding: 4rem 3rem;
            display: flex; flex-direction: column; justify-content: center;
            position: relative; overflow: hidden;
        }
        .fp-side::before, .fp-side::after {
            content: ''; position: absolute; border-radius: 50%;
            filter: blur(50px); pointer-events: none;
        }
        .fp-side::before {
            width: 350px; height: 350px;
            background: rgba(217,119,6,.1);
            top: -80px; right: -80px;
            animation: float 9s ease-in-out infinite;
        }
        .fp-side::after {
            width: 250px; height: 250px;
            background: rgba(217,119,6,.07);
            bottom: -60px; left: -60px;
            animation: float 12s ease-in-out infinite reverse;
        }
        .fp-side > * { position: relative; z-index: 1; }

        .fp-side-title {
            font-size: clamp(1.6rem, 2.5vw, 2.25rem);
            font-weight: 800; color: var(--slate-900);
            line-height: 1.15; margin-bottom: 1rem;
            animation: fadeInUp 0.8s cubic-bezier(0.22,1,0.36,1) both;
            animation-delay: 0.2s;
        }
        .fp-side-title span {
            background: linear-gradient(90deg, var(--amber), #f59e0b, var(--amber));
            background-size: 200% auto;
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer 3s linear infinite;
        }
        .fp-side-sub {
            font-size: .95rem; color: var(--slate-500);
            margin-bottom: 2.5rem; line-height: 1.65;
            animation: fadeInUp 0.8s cubic-bezier(0.22,1,0.36,1) both;
            animation-delay: 0.3s;
        }

        .fp-steps { display: flex; flex-direction: column; gap: 1.25rem; }
        .fp-step {
            display: flex; gap: 1rem; align-items: flex-start;
            padding: 1.25rem;
            background: rgba(255,255,255,.7);
            border: 1px solid rgba(217,119,6,.15);
            border-radius: .75rem;
            backdrop-filter: blur(8px);
            animation: checkIn 0.6s cubic-bezier(0.22,1,0.36,1) both;
        }
        .fp-step:nth-child(1) { animation-delay: 0.35s; }
        .fp-step:nth-child(2) { animation-delay: 0.45s; }
        .fp-step:nth-child(3) { animation-delay: 0.55s; }
        .fp-step-num {
            width: 40px; height: 40px; min-width: 40px;
            border-radius: .5rem; background: var(--amber-lt);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; font-weight: 800; color: var(--amber-dk);
        }
        .fp-step-body strong { display: block; font-size: .9rem; font-weight: 700; color: var(--slate-800); margin-bottom: .2rem; }
        .fp-step-body p { font-size: .82rem; color: var(--slate-500); line-height: 1.5; }

        /* ── Right panel ── */
        .fp-form-panel {
            display: flex; align-items: center; justify-content: center;
            padding: 3rem 2rem; background: var(--white);
        }
        .fp-card {
            width: 100%; max-width: 420px;
            animation: cardEntrance 0.7s cubic-bezier(0.22,1,0.36,1) both;
            animation-delay: 0.1s;
        }
        .fp-badge {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .4rem .9rem; border-radius: 99px;
            background: var(--amber-lt); border: 1px solid rgba(217,119,6,.3);
            font-size: .8rem; font-weight: 700; color: var(--amber-dk);
            margin-bottom: 1.5rem;
        }
        .fp-card-title { font-size: 1.6rem; font-weight: 800; color: var(--slate-900); margin-bottom: .35rem; }
        .fp-card-sub { font-size: .9rem; color: var(--slate-500); margin-bottom: 2rem; line-height: 1.55; }

        /* ── Success alert ── */
        .fp-alert-success {
            padding: 1rem 1.25rem; border-radius: .6rem;
            background: var(--green-50); border: 1px solid var(--green-200);
            color: var(--green-700); font-size: .9rem; line-height: 1.55;
            margin-bottom: 1.5rem;
        }

        /* ── Error global ── */
        .fp-errors {
            padding: .875rem 1.25rem; border-radius: .6rem;
            background: var(--red-50); border: 1px solid var(--red-200);
            margin-bottom: 1.25rem;
        }
        .fp-errors ul { list-style: none; display: flex; flex-direction: column; gap: .35rem; }
        .fp-errors li { font-size: .875rem; color: var(--red-500); }

        /* ── Form elements ── */
        .fp-field { display: flex; flex-direction: column; gap: .45rem; margin-bottom: 1.25rem; }
        .fp-label { font-size: .875rem; font-weight: 600; color: var(--slate-700); }
        .fp-input {
            width: 100%; padding: .7rem .95rem;
            border: 1.5px solid var(--slate-200); border-radius: .5rem;
            font-size: .95rem; color: var(--slate-800);
            background: var(--white); outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .fp-input:focus {
            border-color: var(--amber);
            box-shadow: 0 0 0 3px rgba(217,119,6,.12);
        }
        .fp-input.is-error { border-color: var(--red-500); }
        .fp-field-error { font-size: .8rem; color: var(--red-500); }

        /* ── Submit button ── */
        .fp-btn {
            width: 100%; padding: .8rem;
            background: var(--amber); color: var(--white);
            font-size: .95rem; font-weight: 700;
            border: none; border-radius: .5rem; cursor: pointer;
            box-shadow: 0 2px 8px rgba(217,119,6,.25);
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
        }
        .fp-btn:hover { background: var(--amber-dk); box-shadow: 0 4px 16px rgba(217,119,6,.4); transform: translateY(-1px); }
        .fp-btn:active { transform: scale(.98); }

        /* ── Back link ── */
        .fp-back { text-align: center; margin-top: 1.5rem; font-size: .875rem; color: var(--slate-500); }
        .fp-back a { color: var(--amber); font-weight: 600; text-decoration: none; }
        .fp-back a:hover { text-decoration: underline; }

        /* ── Responsive ── */
        @media (max-width: 900px) {
            .fp-main { grid-template-columns: 1fr; }
            .fp-side { display: none; }
            .fp-form-panel { padding: 2rem 1.25rem; min-height: calc(100vh - 65px); }
        }
        @media (max-width: 480px) {
            .fp-nav { padding: .875rem 1.25rem; }
            .fp-card-title { font-size: 1.35rem; }
        }
    </style>
</head>
<body>

    <nav class="fp-nav">
        <a href="/" class="fp-logo" aria-label="Contabo — Ir al inicio">
            <img src="/images/contabo.png" alt="Contabo" style="height: 3.5rem; display: block;">
        </a>
        <span class="fp-nav-link">
            ¿Recordaste tu contraseña? <a href="{{ route('filament.admin.auth.login') }}">Inicia sesión</a>
        </span>
    </nav>

    <div class="fp-main">

        {{-- Panel izquierdo — pasos ──────────────────────────────────────────── --}}
        <aside class="fp-side" aria-label="Cómo recuperar tu contraseña">
            <h1 class="fp-side-title">Recupera tu <span>acceso</span></h1>
            <p class="fp-side-sub">
                Sin contraseñas enviadas por teléfono ni preguntas de seguridad.<br>
                Solo tres pasos simples y vuelves a estar dentro.
            </p>

            <div class="fp-steps" role="list">
                <div class="fp-step" role="listitem">
                    <div class="fp-step-num" aria-hidden="true">1</div>
                    <div class="fp-step-body">
                        <strong>Ingresa tu correo</strong>
                        <p>Escribe el correo con el que te registraste en Contabo.</p>
                    </div>
                </div>
                <div class="fp-step" role="listitem">
                    <div class="fp-step-num" aria-hidden="true">2</div>
                    <div class="fp-step-body">
                        <strong>Revisa tu bandeja de entrada</strong>
                        <p>Te enviaremos un enlace seguro válido por 60 minutos.</p>
                    </div>
                </div>
                <div class="fp-step" role="listitem">
                    <div class="fp-step-num" aria-hidden="true">3</div>
                    <div class="fp-step-body">
                        <strong>Crea tu nueva contraseña</strong>
                        <p>Elige una contraseña segura y accede de inmediato.</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Panel derecho — formulario ──────────────────────────────────────── --}}
        <section class="fp-form-panel" aria-label="Formulario de recuperación de contraseña">
            <div class="fp-card">
                <div class="fp-badge" role="note">🔑 Recuperar acceso</div>
                <h2 class="fp-card-title">¿Olvidaste tu contraseña?</h2>
                <p class="fp-card-sub">
                    Ingresa tu correo y te enviaremos un enlace para restablecerla.
                </p>

                {{-- Mensaje de éxito ── --}}
                @if (session('status'))
                    <div class="fp-alert-success" role="alert">
                        ✅ {{ session('status') }}
                    </div>
                @endif

                {{-- Errores globales ── --}}
                @if ($errors->any())
                    <div class="fp-errors" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" novalidate>
                    @csrf

                    <div class="fp-field">
                        <label for="email" class="fp-label">Correo electrónico</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="fp-input @error('email') is-error @enderror"
                            value="{{ old('email') }}"
                            placeholder="tu@correo.com"
                            autocomplete="email"
                            autofocus
                            required
                        >
                        @error('email')
                            <span class="fp-field-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="fp-btn">
                        Enviar enlace de restablecimiento
                    </button>
                </form>

                <p class="fp-back">
                    <a href="{{ route('filament.admin.auth.login') }}">← Volver al inicio de sesión</a>
                </p>
            </div>
        </section>

    </div>

</body>
</html>
