@php
    $livewire ??= null;
    $renderHookScopes = $livewire?->getRenderHookScopes();
@endphp

<x-filament-panels::layout.base :livewire="$livewire">

    @push('styles')
        <style>
            :root {
                --amber:    #d97706;
                --amber-dk: #b45309;
                --amber-lt: #fef3c7;
                --slate-50:  #f8fafc;
                --slate-200: #e2e8f0;
                --slate-500: #64748b;
                --slate-800: #1e293b;
                --slate-900: #0f172a;
                --white: #ffffff;
            }

            body.fi-body {
                background: var(--slate-50) !important;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }

            /* ── Animations ── */
            @keyframes navFadeIn {
                from { opacity: 0; transform: translateY(-10px); }
                to   { opacity: 1; transform: translateY(0); }
            }
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(24px); }
                to   { opacity: 1; transform: translateY(0); }
            }
            @keyframes cardEntrance {
                from { opacity: 0; transform: translateY(32px) scale(0.97); }
                to   { opacity: 1; transform: translateY(0) scale(1); }
            }
            @keyframes float {
                0%, 100% { transform: translateY(0); }
                50%       { transform: translateY(-8px); }
            }
            @keyframes shimmer {
                0%   { background-position: -200% center; }
                100% { background-position:  200% center; }
            }
            @keyframes checkIn {
                from { opacity: 0; transform: translateX(-12px); }
                to   { opacity: 1; transform: translateX(0); }
            }
            @keyframes badgePop {
                0%   { opacity: 0; transform: scale(0.85); }
                60%  { transform: scale(1.04); }
                100% { opacity: 1; transform: scale(1); }
            }

            /* ── Nav ── */
            .cbl-nav {
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
            .cbl-logo { text-decoration: none; transition: opacity 0.2s; }
            .cbl-logo:hover { opacity: 0.8; }
            .cbl-nav-link { font-size: .875rem; color: var(--slate-500); }
            .cbl-nav-link a {
                color: var(--amber); font-weight: 600; text-decoration: none;
                position: relative;
            }
            .cbl-nav-link a::after {
                content: '';
                position: absolute; left: 0; bottom: -2px;
                width: 0; height: 2px;
                background: var(--amber);
                border-radius: 99px;
                transition: width 0.25s;
            }
            .cbl-nav-link a:hover::after { width: 100%; }

            /* ── Two-column layout ── */
            .cbl-main {
                display: grid;
                grid-template-columns: 1fr 1fr;
                min-height: calc(100vh - 65px);
            }

            /* ── Left panel ── */
            .cbl-side {
                background: linear-gradient(160deg, var(--slate-50) 0%, var(--amber-lt) 100%);
                padding: 4rem 3rem;
                display: flex;
                flex-direction: column;
                justify-content: center;
                position: relative;
                overflow: hidden;
            }
            .cbl-side::before, .cbl-side::after {
                content: ''; position: absolute; border-radius: 50%;
                filter: blur(50px); pointer-events: none;
            }
            .cbl-side::before {
                width: 350px; height: 350px;
                background: rgba(217,119,6,.1);
                top: -80px; right: -80px;
                animation: float 9s ease-in-out infinite;
            }
            .cbl-side::after {
                width: 250px; height: 250px;
                background: rgba(217,119,6,.07);
                bottom: -60px; left: -60px;
                animation: float 12s ease-in-out infinite reverse;
            }
            .cbl-side > * { position: relative; z-index: 1; }

            .cbl-side-title {
                font-size: clamp(1.6rem, 2.5vw, 2.25rem);
                font-weight: 800; color: var(--slate-900);
                line-height: 1.15; margin-bottom: 1rem;
                animation: fadeInUp 0.8s cubic-bezier(0.22,1,0.36,1) both;
                animation-delay: 0.2s;
            }
            .cbl-side-title span {
                background: linear-gradient(90deg, var(--amber), #f59e0b, var(--amber));
                background-size: 200% auto;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                animation: shimmer 3s linear infinite;
            }
            .cbl-side-sub {
                font-size: .95rem; color: var(--slate-500);
                margin-bottom: 2.5rem; line-height: 1.65;
                animation: fadeInUp 0.8s cubic-bezier(0.22,1,0.36,1) both;
                animation-delay: 0.3s;
            }

            .cbl-props { display: flex; flex-direction: column; gap: 1.25rem; }
            .cbl-prop {
                display: flex; gap: 1rem; align-items: flex-start;
                padding: 1.25rem;
                background: rgba(255,255,255,.7);
                border: 1px solid rgba(217,119,6,.15);
                border-radius: .75rem;
                backdrop-filter: blur(8px);
                transition: transform 0.25s, box-shadow 0.25s;
                animation: checkIn 0.6s cubic-bezier(0.22,1,0.36,1) both;
            }
            .cbl-prop:nth-child(1) { animation-delay: 0.35s; }
            .cbl-prop:nth-child(2) { animation-delay: 0.45s; }
            .cbl-prop:nth-child(3) { animation-delay: 0.55s; }
            .cbl-prop:hover { transform: translateX(4px); box-shadow: 0 4px 16px rgba(217,119,6,.1); }
            .cbl-prop-icon {
                width: 40px; height: 40px; min-width: 40px;
                border-radius: .5rem; background: var(--amber-lt);
                display: flex; align-items: center; justify-content: center;
                font-size: 1.2rem;
            }
            .cbl-prop-body strong {
                display: block; font-size: .9rem; font-weight: 700;
                color: var(--slate-800); margin-bottom: .2rem;
            }
            .cbl-prop-body p { font-size: .82rem; color: var(--slate-500); line-height: 1.5; }

            /* ── Right panel ── */
            .cbl-form-panel {
                display: flex; align-items: center; justify-content: center;
                padding: 3rem 2rem; background: var(--white);
            }
            .cbl-card {
                width: 100%; max-width: 420px;
                animation: cardEntrance 0.7s cubic-bezier(0.22,1,0.36,1) both;
                animation-delay: 0.1s;
            }
            .cbl-badge {
                display: inline-flex; align-items: center; gap: .4rem;
                padding: .4rem .9rem; border-radius: 99px;
                background: var(--amber-lt);
                border: 1px solid rgba(217,119,6,.3);
                font-size: .8rem; font-weight: 700; color: var(--amber-dk);
                margin-bottom: 1.5rem;
                animation: badgePop 0.6s cubic-bezier(0.22,1,0.36,1) both;
                animation-delay: 0.4s;
            }
            .cbl-card-title {
                font-size: 1.6rem; font-weight: 800;
                color: var(--slate-900); margin-bottom: .35rem;
            }
            .cbl-card-sub { font-size: .9rem; color: var(--slate-500); margin-bottom: 2rem; }

            /* ── Ocultamos el encabezado nativo de Filament (logo + heading) ── */
            .cbl-fi-wrap .fi-simple-header { display: none !important; }

            /* ── Inputs de Filament ── */
            .cbl-fi-wrap input[type="email"],
            .cbl-fi-wrap input[type="password"],
            .cbl-fi-wrap input[type="text"] {
                border: 1.5px solid var(--slate-200) !important;
                border-radius: .5rem !important;
                font-size: .95rem !important;
                color: var(--slate-800) !important;
                transition: border-color 0.2s, box-shadow 0.2s !important;
                background: var(--white) !important;
            }
            .cbl-fi-wrap input:focus {
                border-color: var(--amber) !important;
                box-shadow: 0 0 0 3px rgba(217,119,6,.12) !important;
                outline: none !important;
            }

            /* ── Botón primario ── */
            .cbl-fi-wrap .fi-btn-color-primary {
                background: var(--amber) !important;
                border-color: var(--amber) !important;
                color: var(--white) !important;
                font-weight: 700 !important;
                border-radius: .5rem !important;
                box-shadow: 0 2px 8px rgba(217,119,6,.25) !important;
                transition: background 0.2s, transform 0.15s, box-shadow 0.2s !important;
            }
            .cbl-fi-wrap .fi-btn-color-primary:hover {
                background: var(--amber-dk) !important;
                box-shadow: 0 4px 16px rgba(217,119,6,.4) !important;
                transform: translateY(-1px) !important;
            }
            .cbl-fi-wrap .fi-btn-color-primary:active { transform: scale(.98) !important; }

            /* ── Links internos (Olvidé contraseña, etc.) ── */
            .cbl-fi-wrap .fi-link,
            .cbl-fi-wrap .fi-fo-field-wrp-hint a,
            .cbl-fi-wrap a {
                color: var(--amber) !important;
                font-weight: 600;
                text-decoration: none;
            }
            .cbl-fi-wrap a:hover { text-decoration: underline; }

            /* ── Divider y link de registro ── */
            .cbl-forgot-link {
                text-align: center; margin-top: 1rem;
                font-size: .875rem;
            }
            .cbl-forgot-link a { color: var(--amber) !important; font-weight: 600; text-decoration: none; }
            .cbl-forgot-link a:hover { text-decoration: underline; }
            .cbl-divider {
                text-align: center; margin: 1.5rem 0;
                font-size: .82rem; color: #94a3b8;
                display: flex; align-items: center; gap: .75rem;
            }
            .cbl-divider::before, .cbl-divider::after {
                content: ''; flex: 1; height: 1px; background: var(--slate-200);
            }
            .cbl-register-link { text-align: center; font-size: .875rem; color: var(--slate-500); }
            .cbl-register-link a { color: var(--amber) !important; font-weight: 600; }

            /* ── Responsive ── */
            @media (max-width: 900px) {
                .cbl-main { grid-template-columns: 1fr; }
                .cbl-side { display: none; }
                .cbl-form-panel { padding: 2rem 1.25rem; min-height: calc(100vh - 65px); }
            }
            @media (max-width: 480px) {
                .cbl-nav { padding: .875rem 1.25rem; }
                .cbl-card-title { font-size: 1.35rem; }
                .cbl-form-panel { padding: 1.75rem 1rem; align-items: flex-start; padding-top: 2rem; }
            }
        </style>
    @endpush

    <nav class="cbl-nav">
        <a href="/" class="cbl-logo" aria-label="Contabo — Ir al inicio">
            <img src="/images/contabo.png" alt="Contabo" style="height: 3.5rem; display: block;">
        </a>
        <span class="cbl-nav-link">
            ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate gratis</a>
        </span>
    </nav>

    <div class="cbl-main">

        {{-- Panel izquierdo —  value props ──────────────────────────────────── --}}
        <aside class="cbl-side" aria-label="Beneficios de Contabo">
            <h1 class="cbl-side-title">Bienvenido de <span>vuelta</span></h1>
            <p class="cbl-side-sub">
                Tu despacho te espera. Accede para revisar obligaciones,
                facturas y clientes desde donde estés.
            </p>

            <div class="cbl-props" role="list">
                <div class="cbl-prop" role="listitem">
                    <div class="cbl-prop-icon" aria-hidden="true">📅</div>
                    <div class="cbl-prop-body">
                        <strong>Obligaciones al día</strong>
                        <p>Revisa qué declaraciones vencen esta semana para cada uno de tus clientes SAT.</p>
                    </div>
                </div>
                <div class="cbl-prop" role="listitem">
                    <div class="cbl-prop-icon" aria-hidden="true">🧾</div>
                    <div class="cbl-prop-body">
                        <strong>CFDIs organizados</strong>
                        <p>Consulta facturas emitidas y recibidas con búsqueda por RFC, UUID o período.</p>
                    </div>
                </div>
                <div class="cbl-prop" role="listitem">
                    <div class="cbl-prop-icon" aria-hidden="true">📊</div>
                    <div class="cbl-prop-body">
                        <strong>Dashboard en tiempo real</strong>
                        <p>Métricas clave de tu cartera: clientes activos, obligaciones pendientes y más.</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Panel derecho — formulario Filament ──────────────────────────────── --}}
        <section class="cbl-form-panel" aria-label="Formulario de inicio de sesión">
            <div class="cbl-card">
                <div class="cbl-badge" role="note">🔐 Acceso seguro</div>
                <h2 class="cbl-card-title">Iniciar sesión</h2>
                <p class="cbl-card-sub">Ingresa tus credenciales para acceder a tu panel.</p>

                <div class="cbl-fi-wrap">
                    {{ $slot }}
                </div>

                <p class="cbl-forgot-link">
                    <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                </p>

                <div class="cbl-divider">o</div>
                <p class="cbl-register-link">
                    ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate gratis →</a>
                </p>
            </div>
        </section>

    </div>

</x-filament-panels::layout.base>
