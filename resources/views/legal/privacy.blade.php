<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aviso de Privacidad — Contabo</title>
    <meta name="description"
        content="Aviso de Privacidad de Contabo. Conoce cómo tratamos tus datos personales de conformidad con la Ley Federal de Protección de Datos Personales en Posesión de los Particulares (LFPDPPP).">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/aviso-de-privacidad') }}">
    <meta property="og:title" content="Aviso de Privacidad — Contabo">
    <meta property="og:description" content="Conoce cómo protegemos tus datos personales en Contabo.">
    <meta property="og:url" content="{{ url('/aviso-de-privacidad') }}">
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

        /* TOC */
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

        /* Sections */
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
            <img src="/images/contabo.png" alt="Contabo" style="height: 3rem; display: block;">
        </a>
        <a href="/" class="btn-back">← Volver al inicio</a>
    </nav>

    <div class="legal-hero">
        <div class="legal-tag">Legal</div>
        <h1>Aviso de Privacidad</h1>
        <p class="legal-meta">Última actualización: {{ date('d \d\e F \d\e Y') }} · Contabo</p>
    </div>

    <div class="legal-wrap">
        <!-- Tabla de contenido -->
        <nav class="toc" aria-label="Tabla de contenido">
            <h2>Contenido</h2>
            <ol>
                <li><a href="#responsable">Responsable del tratamiento</a></li>
                <li><a href="#datos">Datos personales que recabamos</a></li>
                <li><a href="#finalidades">Finalidades del tratamiento</a></li>
                <li><a href="#transferencias">Transferencias de datos</a></li>
                <li><a href="#derechos-arco">Derechos ARCO</a></li>
                <li><a href="#consentimiento">Consentimiento y uso de cookies</a></li>
                <li><a href="#seguridad">Medidas de seguridad</a></li>
                <li><a href="#datos-terceros">Datos de terceros (clientes del despacho)</a></li>
                <li><a href="#sat">Datos relacionados con el SAT</a></li>
                <li><a href="#cambios">Cambios al aviso</a></li>
                <li><a href="#contacto">Contacto</a></li>
            </ol>
        </nav>

        <div class="highlight-box">
            <strong>Resumen:</strong> En Contabo tratamos tus datos personales con pleno respeto a la
            <strong>Ley Federal de Protección de Datos Personales en Posesión de los Particulares (LFPDPPP)</strong>
            y su Reglamento. Solo usamos los datos estrictamente necesarios para prestarte el servicio.
        </div>

        <!-- I. Responsable -->
        <section class="legal-section" id="responsable">
            <h2><span class="num" aria-hidden="true">1</span> Responsable del tratamiento</h2>
            <p>
                <strong>Contabo</strong> (en adelante, "el Responsable" o "Contabo") es el responsable
                del tratamiento de los datos personales que usted nos proporcione al usar la plataforma disponible
                en <a href="{{ url('/') }}">{{ url('/') }}</a>.
            </p>
            <p>
                Para efectos del presente Aviso de Privacidad, el domicilio del Responsable es el indicado en la
                sección de Contacto al final de este documento.
            </p>
        </section>

        <!-- II. Datos recabados -->
        <section class="legal-section" id="datos">
            <h2><span class="num" aria-hidden="true">2</span> Datos personales que recabamos</h2>
            <p>Dependiendo de la forma en que interactúe con nuestra plataforma, podemos recabar los siguientes
                datos personales:</p>

            <p><strong>Datos de identificación y contacto del titular de la cuenta:</strong></p>
            <ul>
                <li>Nombre completo</li>
                <li>Dirección de correo electrónico</li>
                <li>Contraseña (almacenada en formato cifrado, nunca en texto plano)</li>
            </ul>

            <p><strong>Datos de pago y facturación (procesados por Stripe):</strong></p>
            <ul>
                <li>Información de tarjeta de crédito o débito (número, fecha de vencimiento, CVV).
                    <em>Estos datos los procesa directamente Stripe, Inc. Contabo no almacena datos de tarjeta.</em>
                </li>
                <li>Historial de pagos y facturas de suscripción</li>
            </ul>

            <p><strong>Datos generados por el uso del servicio:</strong></p>
            <ul>
                <li>Dirección IP, tipo de navegador, sistema operativo y cookies de sesión</li>
                <li>Registros de acceso y actividad dentro del panel</li>
                <li>Preferencias de configuración</li>
            </ul>

            <p><strong>Datos sensibles:</strong> Contabo no recaba datos personales sensibles del titular de
                la cuenta (salud, religión, orientación sexual, etc.). No obstante, como parte del servicio, el
                titular puede cargar en la plataforma datos de sus propios clientes (personas físicas o morales),
                incluyendo RFC, e.firma (archivo .cer y .key) y datos fiscales. Ver sección VIII.</p>
        </section>

        <!-- III. Finalidades -->
        <section class="legal-section" id="finalidades">
            <h2><span class="num" aria-hidden="true">3</span> Finalidades del tratamiento</h2>

            <p><strong>Finalidades primarias (necesarias para la prestación del servicio):</strong></p>
            <ul>
                <li>Crear y administrar su cuenta de usuario en Contabo</li>
                <li>Prestar el servicio de gestión contable y fiscal contratado</li>
                <li>Procesar pagos y gestionar su suscripción mensual</li>
                <li>Enviar notificaciones relacionadas con el servicio (vencimientos, pagos, alertas fiscales)</li>
                <li>Atender solicitudes de soporte técnico y consultas</li>
                <li>Cumplir con obligaciones legales y fiscales aplicables</li>
            </ul>

            <p><strong>Finalidades secundarias (puede oponerse):</strong></p>
            <ul>
                <li>Enviar comunicaciones de marketing sobre nuevas funcionalidades o promociones del servicio</li>
                <li>Realizar encuestas de satisfacción del usuario</li>
                <li>Elaborar estadísticas de uso agregadas y anonimizadas</li>
            </ul>

            <p>Si no desea que sus datos se utilicen para las finalidades secundarias, puede manifestarlo
                enviando un correo a la dirección indicada en la sección de Contacto con el asunto
                "Oposición a finalidades secundarias".</p>
        </section>

        <!-- IV. Transferencias -->
        <section class="legal-section" id="transferencias">
            <h2><span class="num" aria-hidden="true">4</span> Transferencias de datos</h2>
            <p>Contabo puede compartir sus datos con los siguientes terceros en los términos de la LFPDPPP:</p>

            <ul>
                <li><strong>Stripe, Inc.</strong> — Procesador de pagos. Tratamiento de datos de tarjeta y
                    facturación. Datos transferidos bajo el estándar PCI-DSS.</li>
                <li><strong>Proveedores de infraestructura en la nube</strong> — Almacenamiento y procesamiento de
                    datos en servidores seguros (ej. hosting, almacenamiento de archivos).</li>
                <li><strong>Proveedores de correo electrónico transaccional</strong> — Para el envío de
                    notificaciones, alertas y correos del sistema.</li>
            </ul>

            <p>Estas transferencias se realizan con proveedores que ofrecen niveles adecuados de protección
                y se encuentran regidos por sus propias políticas de privacidad. Contabo no vende,
                cede ni comercializa sus datos personales a terceros con fines publicitarios o comerciales
                ajenos al servicio.</p>

            <p>Salvo en los casos descritos, Contabo no realizará transferencias de sus datos personales
                sin su consentimiento previo, excepto cuando sea requerido por autoridad competente o
                lo establezca la ley.</p>
        </section>

        <!-- V. ARCO -->
        <section class="legal-section" id="derechos-arco">
            <h2><span class="num" aria-hidden="true">5</span> Derechos ARCO</h2>
            <p>De conformidad con los artículos 28 a 37 de la LFPDPPP, usted tiene derecho a:</p>
            <ul>
                <li><strong>Acceso</strong> — Conocer qué datos personales tenemos sobre usted y cómo los utilizamos.</li>
                <li><strong>Rectificación</strong> — Corregir sus datos cuando sean inexactos o incompletos.</li>
                <li><strong>Cancelación</strong> — Solicitar la eliminación de sus datos de nuestros registros
                    cuando considere que no son necesarios para las finalidades del tratamiento.</li>
                <li><strong>Oposición</strong> — Oponerse al tratamiento de sus datos para finalidades específicas.</li>
            </ul>

            <p>Para ejercer sus derechos ARCO, deberá enviar una solicitud al correo indicado en la sección de
                Contacto con:</p>
            <ol>
                <li>Nombre completo y correo electrónico asociado a su cuenta</li>
                <li>Descripción clara del derecho que desea ejercer</li>
                <li>Documento que acredite su identidad (INE, pasaporte o documento oficial vigente)</li>
            </ol>

            <p>Responderemos su solicitud en un plazo máximo de <strong>20 días hábiles</strong> a partir
                de su recepción, de conformidad con el artículo 32 de la LFPDPPP.</p>
        </section>

        <!-- VI. Consentimiento y cookies -->
        <section class="legal-section" id="consentimiento">
            <h2><span class="num" aria-hidden="true">6</span> Consentimiento y uso de cookies</h2>
            <p>Al crear una cuenta en Contabo y aceptar el presente Aviso de Privacidad, usted otorga su
                consentimiento para el tratamiento de sus datos personales conforme a las finalidades
                primarias descritas en este documento.</p>

            <p><strong>Cookies y tecnologías similares:</strong> Utilizamos cookies de sesión estrictamente
                necesarias para el funcionamiento del panel de administración. Estas cookies:</p>
            <ul>
                <li>Son temporales y se eliminan al cerrar el navegador (cookies de sesión)</li>
                <li>No se usan para rastreo publicitario ni se comparten con terceros con ese fin</li>
                <li>Son necesarias para mantener su sesión activa y las preferencias del panel</li>
            </ul>

            <p>Puede deshabilitar las cookies en su navegador, aunque esto puede afectar el funcionamiento
                del servicio.</p>
        </section>

        <!-- VII. Seguridad -->
        <section class="legal-section" id="seguridad">
            <h2><span class="num" aria-hidden="true">7</span> Medidas de seguridad</h2>
            <p>Contabo implementa medidas técnicas, administrativas y físicas de seguridad para proteger
                sus datos personales contra pérdida, uso indebido, acceso no autorizado, alteración o
                destrucción, que incluyen:</p>
            <ul>
                <li>Cifrado de contraseñas mediante algoritmos de hash seguros (bcrypt)</li>
                <li>Transmisión de datos bajo protocolo HTTPS/TLS</li>
                <li>Control de acceso por roles (Administrador, Capturista, Solo lectura)</li>
                <li>Aislamiento de datos por cuenta (multi-tenancy): cada despacho solo accede a sus propios datos</li>
                <li>Pagos procesados bajo estándar PCI-DSS por Stripe</li>
                <li>Copias de seguridad periódicas de la base de datos</li>
            </ul>
        </section>

        <!-- VIII. Datos de terceros -->
        <section class="legal-section" id="datos-terceros">
            <h2><span class="num" aria-hidden="true">8</span> Datos de terceros (clientes del despacho)</h2>
            <p>
                Como parte del servicio, los usuarios de Contabo (despachos y contadores) pueden
                cargar y gestionar datos personales de sus propios clientes (personas físicas o morales),
                tales como RFC, nombre, CURP, e.firma, domicilio fiscal y documentos.
            </p>
            <p>
                En este supuesto, el <strong>despacho contable actúa como responsable</strong> del tratamiento
                de dichos datos ante sus clientes, y Contabo actúa como <strong>encargado</strong>
                en los términos del artículo 50 de la LFPDPPP. Contabo tratará esos datos
                exclusivamente para prestar el servicio contratado y no los utilizará para ninguna
                otra finalidad.
            </p>
            <p>
                Es responsabilidad del despacho contar con los avisos de privacidad y consentimientos
                correspondientes de sus propios clientes antes de cargar sus datos en la plataforma.
            </p>
        </section>

        <!-- IX. Datos SAT -->
        <section class="legal-section" id="sat">
            <h2><span class="num" aria-hidden="true">9</span> Datos relacionados con el SAT</h2>
            <p>
                Contabo es una plataforma de gestión interna para despachos contables. <strong>No está
                afiliada, patrocinada ni certificada por el Servicio de Administración Tributaria (SAT)</strong>.
                Los datos fiscales (RFC, regímenes, obligaciones, CFDIs) se almacenan y gestionan únicamente
                con la finalidad de apoyar las tareas administrativas del despacho contable, sin que
                Contabo transmita información al SAT en nombre del usuario.
            </p>
            <p>
                La e.firma (archivos .cer y .key) y contraseñas del portal SAT que el usuario registre
                en la plataforma son de uso interno del despacho y se almacenan en repositorios con
                acceso restringido. Contabo no utiliza dichos archivos para realizar trámites
                ante el SAT de manera automatizada.
            </p>
        </section>

        <!-- X. Cambios -->
        <section class="legal-section" id="cambios">
            <h2><span class="num" aria-hidden="true">10</span> Cambios al aviso de privacidad</h2>
            <p>
                Contabo se reserva el derecho de modificar el presente Aviso de Privacidad en
                cualquier momento para adaptarse a cambios normativos, novedades jurisprudenciales
                o cambios en nuestra política de privacidad. Cualquier modificación será notificada
                a través de:
            </p>
            <ul>
                <li>Un aviso visible en la plataforma al iniciar sesión</li>
                <li>Correo electrónico al titular de la cuenta, cuando el cambio sea sustancial</li>
            </ul>
            <p>
                La fecha de última actualización siempre estará indicada al inicio de este documento.
                El uso continuado del servicio después de publicadas las modificaciones implica
                la aceptación de los términos actualizados.
            </p>
        </section>

        <!-- XI. Contacto -->
        <section class="legal-section" id="contacto">
            <h2><span class="num" aria-hidden="true">11</span> Contacto</h2>
            <p>Para cualquier duda, solicitud de ejercicio de derechos ARCO u oposición a finalidades
                secundarias, puede contactarnos en:</p>
            <ul>
                <li><strong>Correo electrónico:</strong> privacidad@contabosaas.mx</li>
                <li><strong>Sitio web:</strong> <a href="{{ url('/') }}">{{ url('/') }}</a></li>
            </ul>
            <p>
                Si considera que su derecho a la protección de datos personales ha sido vulnerado,
                puede acudir ante el <strong>Instituto Nacional de Transparencia, Acceso a la
                Información y Protección de Datos Personales (INAI)</strong> en
                <a href="https://www.inai.org.mx" target="_blank" rel="noopener noreferrer">www.inai.org.mx</a>.
            </p>
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
        <p>© {{ date('Y') }} Contabo · Software de contabilidad para despachos mexicanos</p>
    </footer>
</body>

</html>
