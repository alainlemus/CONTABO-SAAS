<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva contraseña — Contabo</title>
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
            --red-50:  #fef2f2;
            --red-500: #ef4444;
            --red-200: #fca5a5;
            --green-100: #dcfce7;
            --green-600: #16a34a;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--slate-50);
            min-height: 100vh;
        }

        /* ── Animations ── */
        @keyframes navFadeIn    { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInUp     { from { opacity: 0; transform: translateY(24px); }  to { opacity: 1; transform: translateY(0); } }
        @keyframes cardEntrance { from { opacity: 0; transform: translateY(32px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes float        { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
        @keyframes shimmer      { 0% { background-position: -200% center; } 100% { background-position: 200% center; } }

        /* ── Nav ── */
        .rp-nav {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 2rem;
            background: var(--white);
            border-bottom: 1px solid var(--slate-200);
            animation: navFadeIn 0.5s ease both;
            position: relative; z-index: 10;
        }
        .rp-logo { text-decoration: none; transition: opacity 0.2s; }
        .rp-logo:hover { opacity: 0.8; }
        .rp-nav-link { font-size: .875rem; color: var(--slate-500); }
        .rp-nav-link a { color: var(--amber); font-weight: 600; text-decoration: none; position: relative; }
        .rp-nav-link a::after {
            content: ''; position: absolute; left: 0; bottom: -2px;
            width: 0; height: 2px; background: var(--amber);
            border-radius: 99px; transition: width 0.25s;
        }
        .rp-nav-link a:hover::after { width: 100%; }

        /* ── Two-column layout ── */
        .rp-main {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: calc(100vh - 65px);
        }

        /* ── Left panel ── */
        .rp-side {
            background: linear-gradient(160deg, var(--slate-50) 0%, var(--amber-lt) 100%);
            padding: 4rem 3rem;
            display: flex; flex-direction: column; justify-content: center;
            position: relative; overflow: hidden;
        }
        .rp-side::before, .rp-side::after {
            content: ''; position: absolute; border-radius: 50%;
            filter: blur(50px); pointer-events: none;
        }
        .rp-side::before {
            width: 350px; height: 350px; background: rgba(217,119,6,.1);
            top: -80px; right: -80px; animation: float 9s ease-in-out infinite;
        }
        .rp-side::after {
            width: 250px; height: 250px; background: rgba(217,119,6,.07);
            bottom: -60px; left: -60px; animation: float 12s ease-in-out infinite reverse;
        }
        .rp-side > * { position: relative; z-index: 1; }

        .rp-side-title {
            font-size: clamp(1.6rem, 2.5vw, 2.25rem);
            font-weight: 800; color: var(--slate-900);
            line-height: 1.15; margin-bottom: 1rem;
            animation: fadeInUp 0.8s cubic-bezier(0.22,1,0.36,1) both;
            animation-delay: 0.2s;
        }
        .rp-side-title span {
            background: linear-gradient(90deg, var(--amber), #f59e0b, var(--amber));
            background-size: 200% auto;
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text; animation: shimmer 3s linear infinite;
        }
        .rp-side-sub {
            font-size: .95rem; color: var(--slate-500);
            margin-bottom: 2.5rem; line-height: 1.65;
            animation: fadeInUp 0.8s cubic-bezier(0.22,1,0.36,1) both;
            animation-delay: 0.3s;
        }
        .rp-tips { display: flex; flex-direction: column; gap: 1rem; }
        .rp-tip {
            display: flex; gap: .75rem; align-items: flex-start;
            padding: 1rem 1.25rem;
            background: rgba(255,255,255,.7);
            border: 1px solid rgba(217,119,6,.15);
            border-radius: .75rem; backdrop-filter: blur(8px);
            animation: fadeInUp 0.6s cubic-bezier(0.22,1,0.36,1) both;
        }
        .rp-tip:nth-child(1) { animation-delay: 0.35s; }
        .rp-tip:nth-child(2) { animation-delay: 0.45s; }
        .rp-tip:nth-child(3) { animation-delay: 0.55s; }
        .rp-tip-icon { font-size: 1.2rem; line-height: 1.4; }
        .rp-tip p { font-size: .85rem; color: var(--slate-700); line-height: 1.5; }

        /* ── Right panel ── */
        .rp-form-panel {
            display: flex; align-items: center; justify-content: center;
            padding: 3rem 2rem; background: var(--white);
        }
        .rp-card {
            width: 100%; max-width: 420px;
            animation: cardEntrance 0.7s cubic-bezier(0.22,1,0.36,1) both;
            animation-delay: 0.1s;
        }
        .rp-badge {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .4rem .9rem; border-radius: 99px;
            background: var(--amber-lt); border: 1px solid rgba(217,119,6,.3);
            font-size: .8rem; font-weight: 700; color: var(--amber-dk);
            margin-bottom: 1.5rem;
        }
        .rp-card-title { font-size: 1.6rem; font-weight: 800; color: var(--slate-900); margin-bottom: .35rem; }
        .rp-card-sub { font-size: .9rem; color: var(--slate-500); margin-bottom: 2rem; line-height: 1.55; }

        /* ── Errors ── */
        .rp-errors {
            padding: .875rem 1.25rem; border-radius: .6rem;
            background: var(--red-50); border: 1px solid var(--red-200);
            margin-bottom: 1.25rem;
        }
        .rp-errors ul { list-style: none; display: flex; flex-direction: column; gap: .35rem; }
        .rp-errors li { font-size: .875rem; color: var(--red-500); }

        /* ── Form ── */
        .rp-field { display: flex; flex-direction: column; gap: .45rem; margin-bottom: 1.25rem; }
        .rp-label { font-size: .875rem; font-weight: 600; color: var(--slate-700); }
        .rp-input-wrap { position: relative; }
        .rp-input {
            width: 100%; padding: .7rem .95rem;
            border: 1.5px solid var(--slate-200); border-radius: .5rem;
            font-size: .95rem; color: var(--slate-800);
            background: var(--white); outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .rp-input:focus { border-color: var(--amber); box-shadow: 0 0 0 3px rgba(217,119,6,.12); }
        .rp-input.is-error { border-color: var(--red-500); }
        .rp-toggle {
            position: absolute; right: .75rem; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; padding: .25rem;
            color: var(--slate-500); font-size: .9rem; line-height: 1;
        }
        .rp-field-error { font-size: .8rem; color: var(--red-500); }

        /* ── Strength bar ── */
        .rp-strength { margin-top: .5rem; }
        .rp-strength-bar {
            height: 4px; border-radius: 99px; background: var(--slate-200);
            overflow: hidden; margin-bottom: .3rem;
        }
        .rp-strength-fill {
            height: 100%; width: 0; border-radius: 99px;
            transition: width 0.3s, background 0.3s;
        }
        .rp-strength-label { font-size: .75rem; color: var(--slate-500); }

        /* ── Button ── */
        .rp-btn {
            width: 100%; padding: .8rem;
            background: var(--amber); color: var(--white);
            font-size: .95rem; font-weight: 700;
            border: none; border-radius: .5rem; cursor: pointer;
            box-shadow: 0 2px 8px rgba(217,119,6,.25);
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
        }
        .rp-btn:hover { background: var(--amber-dk); box-shadow: 0 4px 16px rgba(217,119,6,.4); transform: translateY(-1px); }
        .rp-btn:active { transform: scale(.98); }

        .rp-back { text-align: center; margin-top: 1.5rem; font-size: .875rem; color: var(--slate-500); }
        .rp-back a { color: var(--amber); font-weight: 600; text-decoration: none; }
        .rp-back a:hover { text-decoration: underline; }

        /* ── Responsive ── */
        @media (max-width: 900px) {
            .rp-main { grid-template-columns: 1fr; }
            .rp-side { display: none; }
            .rp-form-panel { padding: 2rem 1.25rem; min-height: calc(100vh - 65px); }
        }
        @media (max-width: 480px) {
            .rp-nav { padding: .875rem 1.25rem; }
            .rp-card-title { font-size: 1.35rem; }
        }
    </style>
</head>
<body>

    <nav class="rp-nav">
        <a href="/" class="rp-logo" aria-label="Contabo — Ir al inicio">
            <img src="/images/contabo.png" alt="Contabo" style="height: 3.5rem; display: block;">
        </a>
        <span class="rp-nav-link">
            ¿Recordaste tu contraseña? <a href="{{ route('filament.admin.auth.login') }}">Inicia sesión</a>
        </span>
    </nav>

    <div class="rp-main">

        {{-- Panel izquierdo — consejos de contraseña segura ─────────────────── --}}
        <aside class="rp-side" aria-label="Consejos para una contraseña segura">
            <h1 class="rp-side-title">Elige una contraseña <span>segura</span></h1>
            <p class="rp-side-sub">
                Tu cuenta protege información fiscal sensible de tus clientes.<br>
                Una contraseña fuerte es tu primera línea de defensa.
            </p>

            <div class="rp-tips" role="list">
                <div class="rp-tip" role="listitem">
                    <span class="rp-tip-icon" aria-hidden="true">🔢</span>
                    <p>Usa al menos <strong>8 caracteres</strong> combinando letras, números y símbolos.</p>
                </div>
                <div class="rp-tip" role="listitem">
                    <span class="rp-tip-icon" aria-hidden="true">🚫</span>
                    <p>Evita datos personales como tu nombre, RFC o fecha de nacimiento.</p>
                </div>
                <div class="rp-tip" role="listitem">
                    <span class="rp-tip-icon" aria-hidden="true">🔒</span>
                    <p>No reutilices contraseñas de otros servicios o sistemas del SAT.</p>
                </div>
            </div>
        </aside>

        {{-- Panel derecho — formulario ──────────────────────────────────────── --}}
        <section class="rp-form-panel" aria-label="Formulario para nueva contraseña">
            <div class="rp-card">
                <div class="rp-badge" role="note">🔐 Nueva contraseña</div>
                <h2 class="rp-card-title">Restablecer contraseña</h2>
                <p class="rp-card-sub">Ingresa y confirma tu nueva contraseña para recuperar el acceso.</p>

                {{-- Errores globales ── --}}
                @if ($errors->any())
                    <div class="rp-errors" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" novalidate>
                    @csrf

                    {{-- Token oculto ── --}}
                    <input type="hidden" name="token" value="{{ $token }}">

                    {{-- Email ── --}}
                    <div class="rp-field">
                        <label for="email" class="rp-label">Correo electrónico</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="rp-input @error('email') is-error @enderror"
                            value="{{ old('email', request('email')) }}"
                            placeholder="tu@correo.com"
                            autocomplete="email"
                            autofocus
                            required
                        >
                        @error('email')
                            <span class="rp-field-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Nueva contraseña ── --}}
                    <div class="rp-field">
                        <label for="password" class="rp-label">Nueva contraseña</label>
                        <div class="rp-input-wrap">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="rp-input @error('password') is-error @enderror"
                                placeholder="Mínimo 8 caracteres"
                                autocomplete="new-password"
                                oninput="updateStrength(this.value)"
                                required
                            >
                            <button type="button" class="rp-toggle" onclick="toggleVisibility('password', this)" aria-label="Mostrar contraseña">👁</button>
                        </div>

                        {{-- Barra de fortaleza ── --}}
                        <div class="rp-strength" id="strengthWrap" style="display:none;">
                            <div class="rp-strength-bar">
                                <div class="rp-strength-fill" id="strengthFill"></div>
                            </div>
                            <span class="rp-strength-label" id="strengthLabel"></span>
                        </div>

                        @error('password')
                            <span class="rp-field-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Confirmar contraseña ── --}}
                    <div class="rp-field">
                        <label for="password_confirmation" class="rp-label">Confirmar contraseña</label>
                        <div class="rp-input-wrap">
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="rp-input @error('password_confirmation') is-error @enderror"
                                placeholder="Repite tu contraseña"
                                autocomplete="new-password"
                                required
                            >
                            <button type="button" class="rp-toggle" onclick="toggleVisibility('password_confirmation', this)" aria-label="Mostrar confirmación">👁</button>
                        </div>
                        @error('password_confirmation')
                            <span class="rp-field-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="rp-btn">Guardar nueva contraseña</button>
                </form>

                <p class="rp-back">
                    <a href="{{ route('filament.admin.auth.login') }}">← Volver al inicio de sesión</a>
                </p>
            </div>
        </section>

    </div>

    <script>
        function toggleVisibility(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            btn.textContent = isPassword ? '🙈' : '👁';
            btn.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
        }

        function updateStrength(value) {
            const wrap  = document.getElementById('strengthWrap');
            const fill  = document.getElementById('strengthFill');
            const label = document.getElementById('strengthLabel');

            if (!value) { wrap.style.display = 'none'; return; }
            wrap.style.display = 'block';

            let score = 0;
            if (value.length >= 8)  score++;
            if (value.length >= 12) score++;
            if (/[A-Z]/.test(value)) score++;
            if (/[0-9]/.test(value)) score++;
            if (/[^A-Za-z0-9]/.test(value)) score++;

            const levels = [
                { pct: '20%', color: '#ef4444', text: 'Muy débil' },
                { pct: '40%', color: '#f97316', text: 'Débil' },
                { pct: '60%', color: '#eab308', text: 'Regular' },
                { pct: '80%', color: '#84cc16', text: 'Fuerte' },
                { pct: '100%', color: '#22c55e', text: 'Muy fuerte' },
            ];
            const level = levels[Math.min(score, 4)];
            fill.style.width      = level.pct;
            fill.style.background = level.color;
            label.textContent     = level.text;
            label.style.color     = level.color;
        }
    </script>
</body>
</html>
