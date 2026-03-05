<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ContaboSaaS — Software de contabilidad para despachos mexicanos</title>
    <meta name="description" content="Gestiona clientes, facturas CFDI y obligaciones fiscales desde un solo panel. Hecho para contadores en México.">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --amber:     #d97706;
            --amber-dk:  #b45309;
            --amber-lt:  #fef3c7;
            --slate-900: #0f172a;
            --slate-800: #1e293b;
            --slate-700: #334155;
            --slate-500: #64748b;
            --slate-200: #e2e8f0;
            --slate-100: #f1f5f9;
            --slate-50:  #f8fafc;
            --white:     #ffffff;
            --green-100: #dcfce7;
            --green-600: #16a34a;
            --radius:    .75rem;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--slate-800);
            background: var(--white);
            line-height: 1.6;
        }

        a { color: inherit; text-decoration: none; }

        /* ── Nav ─────────────────────────────────────────────────────────────── */
        nav {
            position: sticky; top: 0; z-index: 50;
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 2rem;
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--slate-200);
        }
        .logo { font-size: 1.25rem; font-weight: 800; color: var(--amber); letter-spacing: -.5px; }
        .nav-links { display: flex; align-items: center; gap: 1.5rem; }
        .nav-links a { font-size: .9rem; color: var(--slate-700); font-weight: 500; }
        .nav-links a:hover { color: var(--amber); }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: .6rem 1.25rem; border-radius: .5rem; font-weight: 600; font-size: .9rem; cursor: pointer; border: none; transition: background .15s, transform .1s; }
        .btn:active { transform: scale(.98); }
        .btn-primary { background: var(--amber); color: var(--white); }
        .btn-primary:hover { background: var(--amber-dk); }
        .btn-outline { background: transparent; color: var(--slate-700); border: 1.5px solid var(--slate-200); }
        .btn-outline:hover { border-color: var(--amber); color: var(--amber); }
        .btn-lg { padding: .875rem 2rem; font-size: 1rem; border-radius: var(--radius); }
        .btn-xl { padding: 1rem 2.25rem; font-size: 1.05rem; border-radius: var(--radius); }

        /* ── Hero ────────────────────────────────────────────────────────────── */
        .hero {
            padding: 6rem 2rem 4rem;
            text-align: center;
            background: linear-gradient(160deg, var(--slate-50) 0%, var(--amber-lt) 100%);
        }
        .hero-badge {
            display: inline-block; margin-bottom: 1.25rem;
            padding: .35rem .9rem; border-radius: 99px;
            background: var(--amber-lt); border: 1px solid #fcd34d;
            font-size: .8rem; font-weight: 700; color: var(--amber-dk); letter-spacing: .3px;
            text-transform: uppercase;
        }
        .hero h1 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800; line-height: 1.15;
            color: var(--slate-900);
            max-width: 760px; margin: 0 auto .75rem;
            letter-spacing: -.5px;
        }
        .hero h1 span { color: var(--amber); }
        .hero p {
            font-size: 1.15rem; color: var(--slate-500);
            max-width: 580px; margin: 0 auto 2.25rem;
        }
        .hero-ctas { display: flex; gap: .75rem; justify-content: center; flex-wrap: wrap; }
        .hero-note { margin-top: 1rem; font-size: .82rem; color: var(--slate-500); }

        /* ── Logos / Social proof ─────────────────────────────────────────────── */
        .social-proof {
            padding: 2.5rem 2rem;
            text-align: center;
            border-bottom: 1px solid var(--slate-200);
        }
        .social-proof p { font-size: .85rem; color: var(--slate-500); margin-bottom: 1.5rem; font-weight: 500; text-transform: uppercase; letter-spacing: .5px; }
        .regime-tags { display: flex; flex-wrap: wrap; justify-content: center; gap: .5rem; }
        .regime-tag {
            padding: .4rem .85rem; border-radius: 99px;
            background: var(--slate-100); border: 1px solid var(--slate-200);
            font-size: .82rem; font-weight: 600; color: var(--slate-700);
        }

        /* ── Section wrapper ─────────────────────────────────────────────────── */
        section { padding: 5rem 2rem; }
        .section-inner { max-width: 1100px; margin: 0 auto; }
        .section-label {
            display: inline-block; margin-bottom: .75rem;
            font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .8px;
            color: var(--amber);
        }
        .section-title {
            font-size: clamp(1.6rem, 3vw, 2.25rem);
            font-weight: 800; color: var(--slate-900);
            line-height: 1.2; margin-bottom: .75rem;
        }
        .section-sub { font-size: 1rem; color: var(--slate-500); max-width: 560px; }

        /* ── Features grid ───────────────────────────────────────────────────── */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem; margin-top: 3rem;
        }
        .feature-card {
            padding: 2rem; border-radius: var(--radius);
            border: 1.5px solid var(--slate-200);
            background: var(--white);
            transition: border-color .2s, box-shadow .2s;
        }
        .feature-card:hover { border-color: #fcd34d; box-shadow: 0 4px 20px rgba(217,119,6,.08); }
        .feature-icon {
            width: 48px; height: 48px; border-radius: .6rem;
            background: var(--amber-lt); display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; margin-bottom: 1.25rem;
        }
        .feature-card h3 { font-size: 1.05rem; font-weight: 700; margin-bottom: .5rem; color: var(--slate-900); }
        .feature-card p { font-size: .9rem; color: var(--slate-500); line-height: 1.65; }

        /* ── Obligations section ─────────────────────────────────────────────── */
        .obligations-section { background: var(--slate-50); }
        .obligations-split {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 4rem; align-items: center; margin-top: 3rem;
        }
        @media (max-width: 720px) { .obligations-split { grid-template-columns: 1fr; gap: 2rem; } }
        .obligation-list { display: flex; flex-direction: column; gap: .75rem; }
        .obligation-item {
            display: flex; align-items: center; gap: 1rem;
            padding: 1rem 1.25rem; border-radius: .6rem;
            background: var(--white); border: 1.5px solid var(--slate-200);
        }
        .ob-dot {
            width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;
        }
        .ob-dot.pending  { background: #f59e0b; }
        .ob-dot.presented{ background: var(--green-600); }
        .ob-dot.overdue  { background: #ef4444; }
        .ob-dot.na       { background: var(--slate-500); }
        .obligation-item strong { font-size: .9rem; color: var(--slate-800); }
        .obligation-item span   { font-size: .8rem; color: var(--slate-500); margin-left: auto; }
        .obligations-text .section-sub { margin-bottom: 1.5rem; }
        .check-list { list-style: none; display: flex; flex-direction: column; gap: .6rem; margin-top: 1.5rem; }
        .check-list li { font-size: .92rem; color: var(--slate-700); display: flex; gap: .6rem; align-items: flex-start; }
        .check-list li::before { content: '✓'; color: var(--green-600); font-weight: 700; flex-shrink: 0; margin-top: .05rem; }

        /* ── Pricing ─────────────────────────────────────────────────────────── */
        .pricing-section { background: var(--white); text-align: center; }
        .pricing-card {
            max-width: 420px; margin: 3rem auto 0;
            border: 2px solid var(--amber);
            border-radius: 1.25rem; overflow: hidden;
            box-shadow: 0 8px 40px rgba(217,119,6,.12);
        }
        .pricing-header {
            background: var(--amber); color: var(--white);
            padding: 2rem 2rem 1.5rem;
        }
        .pricing-header h3 { font-size: 1.1rem; font-weight: 700; margin-bottom: .5rem; opacity: .85; }
        .pricing-price { font-size: 3rem; font-weight: 800; letter-spacing: -1px; line-height: 1; }
        .pricing-price sup { font-size: 1.2rem; font-weight: 700; vertical-align: top; margin-top: .5rem; }
        .pricing-price small { font-size: 1rem; font-weight: 400; opacity: .75; }
        .pricing-iva { font-size: .78rem; opacity: .7; margin-top: .35rem; }
        .pricing-body { padding: 2rem; background: var(--white); }
        .pricing-features { list-style: none; text-align: left; margin-bottom: 2rem; display: flex; flex-direction: column; gap: .65rem; }
        .pricing-features li { font-size: .92rem; color: var(--slate-700); display: flex; gap: .6rem; }
        .pricing-features li::before { content: '✓'; color: var(--green-600); font-weight: 700; flex-shrink: 0; }
        .pricing-trial-note { font-size: .82rem; color: var(--slate-500); margin-top: .75rem; }

        /* ── CTA final ───────────────────────────────────────────────────────── */
        .cta-section {
            background: var(--slate-900); color: var(--white);
            text-align: center;
        }
        .cta-section .section-title { color: var(--white); }
        .cta-section p { color: #94a3b8; margin: .75rem auto 2rem; max-width: 480px; }

        /* ── Footer ──────────────────────────────────────────────────────────── */
        footer {
            padding: 2rem; text-align: center;
            border-top: 1px solid var(--slate-200);
            font-size: .82rem; color: var(--slate-500);
        }

        /* ── Responsive nav ──────────────────────────────────────────────────── */
        @media (max-width: 600px) {
            nav { padding: 1rem; }
            .nav-links .nav-hide { display: none; }
        }
    </style>
</head>
<body>

<!-- ── Navegación ──────────────────────────────────────────────────────────── -->
<nav>
    <div class="logo">ContaboSaaS</div>
    <div class="nav-links">
        <a href="#features" class="nav-hide">Funciones</a>
        <a href="#pricing" class="nav-hide">Precios</a>
        <a href="/admin/login" class="btn btn-outline">Iniciar sesión</a>
        <a href="/register" class="btn btn-primary">Probar gratis</a>
    </div>
</nav>

<!-- ── Hero ───────────────────────────────────────────────────────────────── -->
<section class="hero">
    <div class="hero-badge">Hecho para México · Regímenes SAT incluidos</div>
    <h1>El panel que los <span>contadores mexicanos</span> necesitaban</h1>
    <p>Gestiona todos tus clientes, sus facturas CFDI y sus obligaciones fiscales desde un solo lugar. Sin hojas de cálculo, sin caos.</p>
    <div class="hero-ctas">
        <a href="/register" class="btn btn-primary btn-xl">Empezar gratis — 14 días</a>
        <a href="/admin/login" class="btn btn-outline btn-xl">Ya tengo cuenta</a>
    </div>
    <p class="hero-note">Sin tarjeta de crédito para el período de prueba.</p>
</section>

<!-- ── Regímenes ──────────────────────────────────────────────────────────── -->
<div class="social-proof">
    <p>Regímenes fiscales soportados</p>
    <div class="regime-tags">
        <span class="regime-tag">601 · General PM</span>
        <span class="regime-tag">606 · Arrendamiento</span>
        <span class="regime-tag">612 · Act. Empresariales PF</span>
        <span class="regime-tag">621 · RIF</span>
        <span class="regime-tag">625 · Plataformas Tech</span>
        <span class="regime-tag">626 · RESICO</span>
        <span class="regime-tag">605 · Sueldos y Salarios</span>
        <span class="regime-tag">603 · Fines no Lucrativos</span>
    </div>
</div>

<!-- ── Características ────────────────────────────────────────────────────── -->
<section id="features">
    <div class="section-inner">
        <span class="section-label">Funciones</span>
        <h2 class="section-title">Todo lo que tu despacho necesita en un solo panel</h2>
        <p class="section-sub">Diseñado con los flujos reales de un contador mexicano, no con funciones genéricas.</p>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🗂️</div>
                <h3>Expediente de clientes</h3>
                <p>Guarda la información fiscal de cada cliente: RFC, régimen SAT, e.firma (CER/KEY), notas internas y documentos. Todo en un solo expediente.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🧾</div>
                <h3>Facturas CFDI</h3>
                <p>Importa XMLs directamente. El sistema extrae automáticamente UUID, folio, emisor, receptor, monto e impuestos. Descarga el PDF cuando lo necesites.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Obligaciones fiscales</h3>
                <p>Genera automáticamente las obligaciones según el régimen SAT de cada cliente. ISR, IVA, DIOT, IMSS y declaraciones anuales con fechas límite correctas.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Dashboard con reportes</h3>
                <p>Ve de un vistazo cuántas obligaciones están pendientes, cuántas vencidas, y cuántas facturas entraron este mes. Gráfica por estatus y período.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Equipo de trabajo</h3>
                <p>Invita capturistas y asistentes a tu panel. Ellos pueden capturar y editar; tú controlas quién puede eliminar o ver configuración.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Acceso por roles</h3>
                <p>Tres niveles de acceso: Administrador, Capturista y Solo lectura. Cada cliente ve solo sus datos; cada rol solo lo que le corresponde.</p>
            </div>
        </div>
    </div>
</section>

<!-- ── Obligaciones ────────────────────────────────────────────────────────── -->
<section class="obligations-section">
    <div class="section-inner">
        <div class="obligations-split">
            <div class="obligation-list">
                <div class="obligation-item">
                    <div class="ob-dot pending"></div>
                    <strong>ISR Mensual · Empresa ABC · Mayo 2025</strong>
                    <span>Vence 17 jun</span>
                </div>
                <div class="obligation-item">
                    <div class="ob-dot presented"></div>
                    <strong>IVA Mensual · Arrendador López · Abr 2025</strong>
                    <span>Presentada</span>
                </div>
                <div class="obligation-item">
                    <div class="ob-dot overdue"></div>
                    <strong>DIOT · Servicios Tech SA · Mar 2025</strong>
                    <span>Vencida</span>
                </div>
                <div class="obligation-item">
                    <div class="ob-dot na"></div>
                    <strong>IMSS Bimestral · García PF · Ene-Feb 2025</strong>
                    <span>No aplica</span>
                </div>
                <div class="obligation-item">
                    <div class="ob-dot pending"></div>
                    <strong>Declaración Anual PM · Grupo Norte · 2024</strong>
                    <span>Vence 31 mar</span>
                </div>
            </div>
            <div class="obligations-text">
                <span class="section-label">Calendario fiscal</span>
                <h2 class="section-title">Nunca más una obligación vencida</h2>
                <p class="section-sub">El sistema genera automáticamente el calendario fiscal de cada cliente según su régimen SAT, con las fechas límite correctas del SAT.</p>
                <ul class="check-list">
                    <li>Generación automática al asignar el régimen fiscal</li>
                    <li>Fechas límite correctas: día 17, 30 de abril, 31 de marzo</li>
                    <li>Marca obligaciones como presentadas y adjunta el acuse PDF</li>
                    <li>Estatus visual: Pendiente, Presentada, Vencida, No aplica</li>
                    <li>Filtros por período, tipo y estatus</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ── Precios ─────────────────────────────────────────────────────────────── -->
<section id="pricing" class="pricing-section">
    <div class="section-inner">
        <span class="section-label">Precios</span>
        <h2 class="section-title">Un plan. Sin sorpresas.</h2>
        <p class="section-sub" style="margin: 0 auto;">Sin límites de clientes, sin funciones bloqueadas, sin planes confusos. Todo incluido desde el primer día.</p>

        <div class="pricing-card">
            <div class="pricing-header">
                <h3>Plan Despacho</h3>
                <div class="pricing-price">
                    <sup>$</sup>399<small> MXN/mes</small>
                </div>
                <p class="pricing-iva">+ IVA · Factura disponible</p>
            </div>
            <div class="pricing-body">
                <ul class="pricing-features">
                    <li>Clientes y expedientes ilimitados</li>
                    <li>Gestión de facturas CFDI con importación XML</li>
                    <li>Obligaciones fiscales con calendario automático</li>
                    <li>Dashboard con gráficas y reportes</li>
                    <li>Equipo: capturistas y viewers incluidos</li>
                    <li>Almacenamiento de e.firma y acuses PDF</li>
                    <li>Soporte por correo electrónico</li>
                </ul>
                <a href="/register" class="btn btn-primary btn-lg" style="width:100%; margin-bottom:.5rem">
                    Empezar gratis — 14 días
                </a>
                <p class="pricing-trial-note">Sin tarjeta de crédito durante el trial. Cancela cuando quieras.</p>
            </div>
        </div>
    </div>
</section>

<!-- ── CTA final ───────────────────────────────────────────────────────────── -->
<section class="cta-section">
    <div class="section-inner">
        <h2 class="section-title">Empieza a ordenar tu despacho hoy</h2>
        <p>14 días gratis, sin tarjeta. Si no te convence, no pagas nada.</p>
        <a href="/register" class="btn btn-primary btn-xl">Crear cuenta gratis</a>
    </div>
</section>

<!-- ── Footer ─────────────────────────────────────────────────────────────── -->
<footer>
    <p>© {{ date('Y') }} ContaboSaaS · Software de contabilidad para despachos mexicanos</p>
</footer>

</body>
</html>
