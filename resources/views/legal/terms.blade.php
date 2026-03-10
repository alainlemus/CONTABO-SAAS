<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos y Condiciones — ContaboSaaS</title>
    <meta name="description"
        content="Términos y Condiciones de uso de ContaboSaaS. Lee los derechos, obligaciones y condiciones del servicio de software contable para despachos en México.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/terminos-y-condiciones') }}">
    <meta property="og:title" content="Términos y Condiciones — ContaboSaaS">
    <meta property="og:description"
        content="Conoce los términos de uso del software de contabilidad ContaboSaaS para despachos en México.">
    <meta property="og:url" content="{{ url('/terminos-y-condiciones') }}">
    <link rel="icon" type="image/png" href="/images/favicon.png">

    <style>
        :root {
            --amber:     #d97706;
            --amber-dk:  #b45309;
            --amber-lt:  #fef3c7;
            --slate-50:  #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-500: #64748b;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
            --white:     #ffffff;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--slate-800);
            background: var(--white);
            line-height: 1.7;
        }

        a { color: var(--amber); text-decoration: none; }
        a:hover { text-decoration: underline; }

        @keyframes navFadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Nav ─────────────────────────────────────────────────────────────── */
        nav {
            position: sticky;
            top: 0;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 2rem;
            background: rgba(255,255,255,.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--slate-200);
            animation: navFadeIn 0.4s ease both;
        }

        .logo { transition: opacity 0.2s; }
        .logo:hover { opacity: 0.8; }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-size: .875rem;
            font-weight: 600;
            color: var(--slate-700);
            padding: .5rem 1rem;
            border-radius: .5rem;
            border: 1.5px solid var(--slate-200);
            transition: border-color 0.2s, color 0.2s;
        }

        .btn-back:hover { border-color: var(--amber); color: var(--amber); text-decoration: none; }

        /* ── Hero ────────────────────────────────────────────────────────────── */
        .legal-hero {
            background: linear-gradient(160deg, var(--slate-50) 0%, var(--amber-lt) 100%);
            padding: 4rem 2rem 3rem;
            text-align: center;
            animation: fadeInUp 0.6s ease both;
        }

        .legal-tag {
            display: inline-block;
            padding: .35rem .9rem;
            border-radius: 99px;
            background: var(--amber-lt);
            border: 1px solid #fcd34d;
            font-size: .78rem;
            font-weight: 700;
            color: var(--amber-dk);
            letter-spacing: .4px;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .legal-hero h1 {
            font-size: clamp(1.75rem, 4vw, 2.5rem);
            font-weight: 800;
            color: var(--slate-900);
            margin-bottom: .5rem;
        }

        .legal-meta {
            font-size: .85rem;
            color: var(--slate-500);
            margin-top: .5rem;
        }

        /* ── Content ─────────────────────────────────────────────────────────── */
        .legal-wrap {
            max-width: 820px;
            margin: 0 auto;
            padding: 3rem 2rem 5rem;
        }

        .toc {
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: .75rem;
            padding: 1.5rem 1.75rem;
            margin-bottom: 3rem;
        }

        .toc h2 {
            font-size: .85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--slate-500);
            margin-bottom: 1rem;
        }

        .toc ol {
            padding-left: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: .35rem;
        }

        .toc li { font-size: .88rem; }
        .toc a { color: var(--slate-700); font-weight: 500; }
        .toc a:hover { color: var(--amber); text-decoration: none; }

        .legal-section {
            margin-bottom: 2.75rem;
            scroll-margin-top: 80px;
        }

        .legal-section h2 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--slate-900);
            margin-bottom: .75rem;
            padding-bottom: .5rem;
            border-bottom: 2px solid var(--amber-lt);
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .legal-section h2 .num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            min-width: 26px;
            border-radius: 50%;
            background: var(--amber);
            color: var(--white);
            font-size: .75rem;
            font-weight: 800;
        }

        .legal-section p {
            font-size: .95rem;
            color: var(--slate-700);
            margin-bottom: .85rem;
            line-height: 1.75;
        }

        .legal-section ul,
        .legal-section ol {
            padding-left: 1.5rem;
            margin-bottom: .85rem;
        }

        .legal-section li {
            font-size: .95rem;
            color: var(--slate-700);
            margin-bottom: .4rem;
            line-height: 1.65;
        }

        .legal-section strong { color: var(--slate-900); }

        .highlight-box {
            background: var(--amber-lt);
            border-left: 4px solid var(--amber);
            border-radius: 0 .5rem .5rem 0;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            font-size: .9rem;
            color: var(--amber-dk);
        }

        .warning-box {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            border-radius: 0 .5rem .5rem 0;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            font-size: .9rem;
            color: #991b1b;
        }

        /* ── Footer ──────────────────────────────────────────────────────────── */
        footer {
            padding: 2rem;
            text-align: center;
            border-top: 1px solid var(--slate-200);
            font-size: .82rem;
            color: var(--slate-500);
            background: var(--slate-50);
        }

        .footer-links {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
            margin-bottom: .5rem;
        }

        .footer-links a { color: var(--slate-500); font-size: .82rem; }
        .footer-links a:hover { color: var(--amber); text-decoration: none; }

        @media (max-width: 600px) {
            nav { padding: .875rem 1.25rem; }
            .legal-hero { padding: 3rem 1.25rem 2rem; }
            .legal-wrap { padding: 2rem 1.25rem 4rem; }
        }
    </style>
</head>

<body>
    <nav>
        <a href="/" class="logo">
            <img src="/images/contabo.png" alt="ContaboSaaS" style="height: 3rem; display: block;">
        </a>
        <a href="/" class="btn-back">← Volver al inicio</a>
    </nav>

    <div class="legal-hero">
        <div class="legal-tag">Legal</div>
        <h1>Términos y Condiciones</h1>
        <p class="legal-meta">Última actualización: {{ date('d \d\e F \d\e Y') }} · ContaboSaaS</p>
    </div>

    <div class="legal-wrap">
        <nav class="toc" aria-label="Tabla de contenido">
            <h2>Contenido</h2>
            <ol>
                <li><a href="#definiciones">Definiciones</a></li>
                <li><a href="#aceptacion">Aceptación de los términos</a></li>
                <li><a href="#descripcion">Descripción del servicio</a></li>
                <li><a href="#registro">Registro y cuenta de usuario</a></li>
                <li><a href="#suscripcion">Suscripción y pago</a></li>
                <li><a href="#trial">Período de prueba gratuita</a></li>
                <li><a href="#uso-aceptable">Uso aceptable</a></li>
                <li><a href="#propiedad">Propiedad intelectual</a></li>
                <li><a href="#datos">Datos y privacidad</a></li>
                <li><a href="#sat-compliance">Cumplimiento fiscal y SAT</a></li>
                <li><a href="#disponibilidad">Disponibilidad del servicio</a></li>
                <li><a href="#responsabilidad">Limitación de responsabilidad</a></li>
                <li><a href="#cancelacion">Cancelación y terminación</a></li>
                <li><a href="#ley-aplicable">Ley aplicable y jurisdicción</a></li>
                <li><a href="#modificaciones">Modificaciones a los términos</a></li>
                <li><a href="#contacto">Contacto</a></li>
            </ol>
        </nav>

        <div class="highlight-box">
            <strong>Importante:</strong> Lea detenidamente estos Términos y Condiciones antes de usar
            ContaboSaaS. Al crear una cuenta o usar el servicio, usted acepta quedar obligado por
            estos términos. Si no está de acuerdo, no utilice el servicio.
        </div>

        <!-- I. Definiciones -->
        <section class="legal-section" id="definiciones">
            <h2><span class="num" aria-hidden="true">1</span> Definiciones</h2>
            <p>Para efectos de los presentes Términos y Condiciones, se entenderá por:</p>
            <ul>
                <li><strong>"ContaboSaaS" / "el Proveedor" / "nosotros":</strong> La plataforma de software
                    como servicio disponible en <a href="{{ url('/') }}">{{ url('/') }}</a> y sus operadores.</li>
                <li><strong>"Usuario" / "usted":</strong> La persona física o moral que contrata y usa el servicio,
                    generalmente un despacho contable, contador o profesional fiscal.</li>
                <li><strong>"Cuenta":</strong> El acceso personalizado al panel de administración de ContaboSaaS,
                    identificado por correo electrónico y contraseña.</li>
                <li><strong>"Suscripción":</strong> El plan de pago mensual que habilita el acceso completo
                    al servicio.</li>
                <li><strong>"Clientes del despacho":</strong> Las personas físicas o morales cuyos datos
                    fiscales gestiona el Usuario dentro de la plataforma.</li>
                <li><strong>"CFDI":</strong> Comprobante Fiscal Digital por Internet, emitido conforme a las
                    disposiciones del SAT.</li>
                <li><strong>"SAT":</strong> Servicio de Administración Tributaria de México.</li>
                <li><strong>"Contenido":</strong> Cualquier dato, archivo, documento o información que el
                    Usuario cargue o genere en la plataforma.</li>
            </ul>
        </section>

        <!-- II. Aceptación -->
        <section class="legal-section" id="aceptacion">
            <h2><span class="num" aria-hidden="true">2</span> Aceptación de los términos</h2>
            <p>
                Al registrarse en ContaboSaaS, usar el servicio o hacer clic en "Crear cuenta gratis",
                usted declara que:
            </p>
            <ul>
                <li>Ha leído y entendido los presentes Términos y Condiciones.</li>
                <li>Acepta quedar vinculado jurídicamente por ellos y por nuestro
                    <a href="{{ route('legal.privacy') }}">Aviso de Privacidad</a>.</li>
                <li>Cuenta con la capacidad legal para celebrar contratos en México (ser mayor de 18 años
                    o actuar en representación de una persona moral).</li>
                <li>La información que proporciona es verdadera, exacta y completa.</li>
            </ul>
        </section>

        <!-- III. Descripción -->
        <section class="legal-section" id="descripcion">
            <h2><span class="num" aria-hidden="true">3</span> Descripción del servicio</h2>
            <p>ContaboSaaS es una plataforma SaaS (Software as a Service) diseñada para despachos
                contables y contadores en México. El servicio incluye, de manera enunciativa más no
                limitativa:</p>
            <ul>
                <li>Gestión de expedientes de clientes con datos fiscales (RFC, régimen SAT, e.firma)</li>
                <li>Importación y almacenamiento de Comprobantes Fiscales Digitales (CFDI) en formato XML</li>
                <li>Generación automática del calendario de obligaciones fiscales según el régimen SAT de cada cliente</li>
                <li>Seguimiento y control de estatus de obligaciones fiscales (ISR, IVA, DIOT, IMSS,
                    declaraciones anuales, entre otras)</li>
                <li>Dashboard con estadísticas, gráficas y reportes exportables</li>
                <li>Gestión de equipo de trabajo con roles diferenciados (Administrador, Capturista, Visor)</li>
                <li>Notificaciones por correo electrónico sobre vencimientos y eventos importantes</li>
                <li>Almacenamiento de documentos y acuses PDF</li>
            </ul>
            <p>
                ContaboSaaS es una herramienta de gestión y organización interna. <strong>No es un
                proveedor autorizado de timbrado de CFDI</strong>, no genera o expide comprobantes fiscales
                ante el SAT, y no realiza declaraciones fiscales en nombre del usuario.
            </p>
        </section>

        <!-- IV. Registro -->
        <section class="legal-section" id="registro">
            <h2><span class="num" aria-hidden="true">4</span> Registro y cuenta de usuario</h2>
            <p>Para acceder al servicio deberá crear una cuenta proporcionando:</p>
            <ul>
                <li>Nombre completo</li>
                <li>Dirección de correo electrónico válida</li>
                <li>Contraseña segura (mínimo 8 caracteres)</li>
            </ul>
            <p>El usuario es el único responsable de:</p>
            <ul>
                <li>Mantener la confidencialidad de sus credenciales de acceso.</li>
                <li>Todas las actividades realizadas desde su cuenta.</li>
                <li>Notificar de inmediato a ContaboSaaS cualquier uso no autorizado de su cuenta.</li>
            </ul>
            <p>
                ContaboSaaS no será responsable por pérdidas derivadas del uso no autorizado de
                su cuenta cuando el usuario no haya tomado las medidas razonables de seguridad.
            </p>
        </section>

        <!-- V. Suscripción y pago -->
        <section class="legal-section" id="suscripcion">
            <h2><span class="num" aria-hidden="true">5</span> Suscripción y pago</h2>
            <p><strong>Plan y precio:</strong> ContaboSaaS opera bajo un modelo de suscripción mensual.
                El precio vigente es de <strong>$399 MXN al mes</strong> (más IVA cuando aplique),
                sujeto a cambios con previo aviso de al menos 30 días.</p>

            <p><strong>Cobro:</strong> El pago se realiza de forma recurrente cada mes a través de
                <strong>Stripe, Inc.</strong>, procesador de pagos certificado PCI-DSS. Al ingresar
                su método de pago, autoriza los cobros recurrentes.</p>

            <p><strong>Facturación:</strong> Si requiere comprobante fiscal (CFDI) por su suscripción,
                deberá solicitarlo con sus datos fiscales a través de los canales de soporte indicados
                en la sección de Contacto.</p>

            <p><strong>Falta de pago:</strong> En caso de que el pago no pueda procesarse, ContaboSaaS
                podrá suspender el acceso al servicio hasta regularizar el adeudo. Si el adeudo
                persiste por más de 30 días, la cuenta podrá cancelarse definitivamente.</p>

            <p><strong>No reembolsos:</strong> Los pagos realizados por períodos transcurridos
                no son reembolsables, salvo en los casos expresamente previstos en estos términos
                o por disposición legal aplicable.</p>
        </section>

        <!-- VI. Trial -->
        <section class="legal-section" id="trial">
            <h2><span class="num" aria-hidden="true">6</span> Período de prueba gratuita</h2>
            <p>
                Al registrarse, el usuario tendrá acceso a <strong>14 días de prueba gratuita</strong>
                con acceso completo al servicio, sin necesidad de proporcionar datos de tarjeta de crédito.
            </p>
            <p>
                Al término del período de prueba, si el usuario desea continuar usando el servicio,
                deberá contratar una suscripción mensual. Si no lo hace, el acceso quedará suspendido
                automáticamente al vencer el período de prueba.
            </p>
            <p>
                ContaboSaaS se reserva el derecho de modificar la duración del período de prueba
                sin previo aviso para nuevos registros. Los usuarios ya registrados conservarán
                su período de prueba original.
            </p>
        </section>

        <!-- VII. Uso aceptable -->
        <section class="legal-section" id="uso-aceptable">
            <h2><span class="num" aria-hidden="true">7</span> Uso aceptable</h2>
            <p>Al usar ContaboSaaS, el usuario se compromete a <strong>no</strong>:</p>
            <ul>
                <li>Usar el servicio para actividades ilegales, fraudulentas o que violen la legislación
                    mexicana aplicable, incluyendo el Código Fiscal de la Federación (CFF).</li>
                <li>Cargar, transmitir o almacenar contenido que infrinja derechos de terceros.</li>
                <li>Intentar acceder sin autorización a sistemas, servidores o cuentas de otros usuarios.</li>
                <li>Realizar ingeniería inversa, descompilar o intentar obtener el código fuente del software.</li>
                <li>Revender, sublicenciar o comercializar el acceso al servicio a terceros sin autorización.</li>
                <li>Cargar malware, virus o código malicioso de cualquier tipo.</li>
                <li>Usar el servicio para fines distintos a la gestión contable y fiscal legítima.</li>
                <li>Sobrecargar intencionalmente la infraestructura del servicio.</li>
            </ul>
            <p>
                ContaboSaaS se reserva el derecho de suspender o cancelar cuentas que violen
                las presentes condiciones de uso, sin responsabilidad y sin necesidad de aviso previo
                cuando la violación sea grave.
            </p>
        </section>

        <!-- VIII. Propiedad intelectual -->
        <section class="legal-section" id="propiedad">
            <h2><span class="num" aria-hidden="true">8</span> Propiedad intelectual</h2>
            <p>
                Todo el software, diseño, código fuente, logotipos, marcas, textos, gráficas
                y demás elementos de ContaboSaaS son propiedad exclusiva del Proveedor y están
                protegidos por la <strong>Ley Federal del Derecho de Autor</strong> y la
                <strong>Ley de la Propiedad Industrial</strong>, así como los tratados internacionales
                aplicables.
            </p>
            <p>
                La suscripción otorga al usuario una <strong>licencia de uso no exclusiva, no transferible
                y revocable</strong> para utilizar el servicio durante la vigencia de su suscripción,
                únicamente para los fines descritos en estos términos.
            </p>
            <p>
                <strong>Contenido del usuario:</strong> El usuario conserva todos los derechos sobre
                los datos e información que cargue en la plataforma. Al usar el servicio, otorga a
                ContaboSaaS una licencia limitada para procesar y almacenar dicho contenido
                exclusivamente para prestar el servicio contratado.
            </p>
        </section>

        <!-- IX. Datos y privacidad -->
        <section class="legal-section" id="datos">
            <h2><span class="num" aria-hidden="true">9</span> Datos y privacidad</h2>
            <p>
                El tratamiento de datos personales se rige por nuestro
                <a href="{{ route('legal.privacy') }}">Aviso de Privacidad</a>, el cual forma parte
                integrante de estos Términos y Condiciones.
            </p>
            <p>
                El usuario es responsable de contar con los consentimientos y avisos de privacidad
                necesarios de los clientes de su despacho antes de ingresar sus datos a la plataforma,
                de conformidad con la <strong>Ley Federal de Protección de Datos Personales en
                Posesión de los Particulares (LFPDPPP)</strong>.
            </p>
        </section>

        <!-- X. SAT compliance -->
        <section class="legal-section" id="sat-compliance">
            <h2><span class="num" aria-hidden="true">10</span> Cumplimiento fiscal y SAT</h2>
            <div class="warning-box">
                <strong>Aviso importante:</strong> ContaboSaaS es una herramienta de gestión y organización.
                No reemplaza la responsabilidad del contador o del contribuyente ante el SAT.
            </div>
            <p>
                ContaboSaaS facilita el seguimiento y organización de obligaciones fiscales, pero:
            </p>
            <ul>
                <li>No genera ni presenta declaraciones fiscales ante el SAT en nombre del usuario.</li>
                <li>No timbra CFDI ni actúa como PAC (Proveedor Autorizado de Certificación).</li>
                <li>Las fechas límite de obligaciones generadas son referenciales y pueden estar sujetas
                    a cambios por el SAT o por la situación fiscal particular del contribuyente.</li>
                <li>El usuario es el único responsable de verificar la correcta y oportuna presentación
                    de sus declaraciones y pagos ante el SAT.</li>
            </ul>
            <p>
                ContaboSaaS no asume responsabilidad por multas, recargos, actualizaciones o
                sanciones del SAT derivadas del incumplimiento fiscal del usuario o de sus clientes.
            </p>
        </section>

        <!-- XI. Disponibilidad -->
        <section class="legal-section" id="disponibilidad">
            <h2><span class="num" aria-hidden="true">11</span> Disponibilidad del servicio</h2>
            <p>
                ContaboSaaS procura mantener el servicio disponible <strong>24 horas al día, 7 días
                a la semana</strong>, pero no garantiza disponibilidad ininterrumpida. El servicio
                puede estar temporalmente no disponible por:
            </p>
            <ul>
                <li>Mantenimiento programado (se notificará con antelación razonable)</li>
                <li>Fallas técnicas o de infraestructura de terceros proveedores</li>
                <li>Causas de fuerza mayor o caso fortuito</li>
                <li>Actos o restricciones de autoridad</li>
            </ul>
            <p>
                ContaboSaaS realizará sus mejores esfuerzos para minimizar las interrupciones del servicio
                y notificar a los usuarios con la mayor antelación posible.
            </p>
        </section>

        <!-- XII. Limitación de responsabilidad -->
        <section class="legal-section" id="responsabilidad">
            <h2><span class="num" aria-hidden="true">12</span> Limitación de responsabilidad</h2>
            <p>En la máxima medida permitida por la legislación mexicana aplicable:</p>
            <ul>
                <li>ContaboSaaS no será responsable por daños indirectos, incidentales, especiales,
                    consecuentes o punitivos derivados del uso o imposibilidad de uso del servicio.</li>
                <li>La responsabilidad total acumulada de ContaboSaaS frente al usuario no excederá
                    el importe pagado por el usuario en los últimos 3 meses de suscripción.</li>
                <li>ContaboSaaS no garantiza que el servicio esté libre de errores, que los resultados
                    sean precisos o que satisfaga todos los requerimientos del usuario.</li>
                <li>El usuario es el único responsable de sus decisiones contables, fiscales y
                    administrativas tomadas con base en la información gestionada en la plataforma.</li>
            </ul>
            <p>
                Lo anterior no aplica en casos de dolo o culpa grave imputable a ContaboSaaS.
            </p>
        </section>

        <!-- XIII. Cancelación -->
        <section class="legal-section" id="cancelacion">
            <h2><span class="num" aria-hidden="true">13</span> Cancelación y terminación</h2>
            <p><strong>Por el usuario:</strong> Puede cancelar su suscripción en cualquier momento
                desde el panel de facturación dentro de la plataforma o contactando a soporte.
                La cancelación será efectiva al término del período pagado; no se realizarán
                reembolsos proporcionales por días no utilizados.</p>

            <p><strong>Por ContaboSaaS:</strong> Nos reservamos el derecho de suspender o terminar
                el acceso al servicio en los siguientes casos:</p>
            <ul>
                <li>Incumplimiento de estos Términos y Condiciones</li>
                <li>Falta de pago por más de 30 días</li>
                <li>Uso fraudulento o ilegal del servicio</li>
                <li>Por decisión unilateral del Proveedor, con aviso previo de al menos 30 días</li>
            </ul>

            <p><strong>Datos tras la cancelación:</strong> Una vez cancelada la cuenta, ContaboSaaS
                conservará los datos del usuario por un período de <strong>30 días naturales</strong>
                para permitir su descarga o exportación. Transcurrido dicho período, los datos
                podrán eliminarse de manera definitiva.</p>
        </section>

        <!-- XIV. Ley aplicable -->
        <section class="legal-section" id="ley-aplicable">
            <h2><span class="num" aria-hidden="true">14</span> Ley aplicable y jurisdicción</h2>
            <p>
                Los presentes Términos y Condiciones se rigen e interpretan de conformidad con las
                leyes vigentes en los <strong>Estados Unidos Mexicanos</strong>, incluyendo de manera
                enunciativa:
            </p>
            <ul>
                <li>Código de Comercio</li>
                <li>Ley Federal de Protección al Consumidor</li>
                <li>Código Civil Federal</li>
                <li>Ley Federal de Protección de Datos Personales en Posesión de los Particulares</li>
                <li>Ley Federal del Derecho de Autor</li>
            </ul>
            <p>
                Para la resolución de cualquier controversia derivada de la interpretación o
                cumplimiento de estos Términos, las partes se someten a la jurisdicción de los
                <strong>Tribunales competentes de la Ciudad de México</strong>, renunciando expresamente
                a cualquier otro fuero que pudiera corresponderles por razón de su domicilio presente
                o futuro.
            </p>
        </section>

        <!-- XV. Modificaciones -->
        <section class="legal-section" id="modificaciones">
            <h2><span class="num" aria-hidden="true">15</span> Modificaciones a los términos</h2>
            <p>
                ContaboSaaS podrá modificar estos Términos y Condiciones en cualquier momento.
                Los cambios serán notificados a través de:
            </p>
            <ul>
                <li>Un aviso destacado en la plataforma al iniciar sesión</li>
                <li>Correo electrónico al titular de la cuenta, con al menos <strong>15 días de
                    anticipación</strong> para cambios sustanciales</li>
            </ul>
            <p>
                El uso continuado del servicio después de la fecha de entrada en vigor de las
                modificaciones implica la aceptación de los nuevos términos. Si el usuario no
                acepta los cambios, podrá cancelar su suscripción antes de la fecha efectiva.
            </p>
        </section>

        <!-- XVI. Contacto -->
        <section class="legal-section" id="contacto">
            <h2><span class="num" aria-hidden="true">16</span> Contacto</h2>
            <p>Para consultas, soporte o cualquier asunto relacionado con estos Términos y Condiciones:</p>
            <ul>
                <li><strong>Correo electrónico:</strong> legal@contabosaas.mx</li>
                <li><strong>Soporte general:</strong> soporte@contabosaas.mx</li>
                <li><strong>Sitio web:</strong> <a href="{{ url('/') }}">{{ url('/') }}</a></li>
            </ul>
        </section>
    </div>

    <footer>
        <div class="footer-links">
            <a href="{{ route('legal.privacy') }}">Aviso de Privacidad</a>
            <span style="color: var(--slate-200);">·</span>
            <a href="{{ route('legal.terms') }}">Términos y Condiciones</a>
            <span style="color: var(--slate-200);">·</span>
            <a href="/">Inicio</a>
        </div>
        <p>© {{ date('Y') }} ContaboSaaS · Software de contabilidad para despachos mexicanos</p>
    </footer>
</body>

</html>
