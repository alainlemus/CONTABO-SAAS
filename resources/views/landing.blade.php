<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- ── SEO Primary ──────────────────────────────────────────────────────── --}}
    <title>Contabo — Software de contabilidad fiscal para despachos y contadores en México</title>
    <meta name="description"
        content="Gestiona clientes, facturas CFDI y obligaciones fiscales desde un solo panel. Automatiza el calendario SAT, importa XMLs y trabaja con tu equipo. Prueba 14 días gratis.">
    <meta name="keywords"
        content="software contabilidad México, gestión obligaciones fiscales SAT, software despacho contable, CFDI XML, régimen fiscal, ISR IVA DIOT, software contador México">
    <meta name="robots" content="index, follow">
    <meta name="author" content="ContaboSaaS">
    <link rel="canonical" href="{{ url('/') }}">

    {{-- ── Open Graph ───────────────────────────────────────────────────────── --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="ContaboSaaS — Software fiscal para despachos mexicanos">
    <meta property="og:description"
        content="El panel que los contadores mexicanos necesitaban. Gestiona clientes, CFDI y obligaciones SAT desde un solo lugar. 14 días gratis.">
    <meta property="og:image" content="{{ asset('images/contabo.png') }}">
    <meta property="og:locale" content="es_MX">
    <meta property="og:site_name" content="ContaboSaaS">

    {{-- ── Twitter Card ─────────────────────────────────────────────────────── --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="ContaboSaaS — Software fiscal para despachos mexicanos">
    <meta name="twitter:description"
        content="Gestiona clientes, CFDI y obligaciones SAT desde un solo panel. Prueba 14 días gratis.">
    <meta name="twitter:image" content="{{ asset('images/contabo.png') }}">

    {{-- ── Schema.org JSON-LD ───────────────────────────────────────────────── --}}
    <script type="application/ld+json">
    @php
        $siteUrl = url('/');
        $schemaData = [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => 'ContaboSaaS',
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web',
            'description' => 'Software de gestión contable y fiscal para despachos y contadores en México. Automatiza obligaciones SAT, gestiona CFDIs y administra clientes.',
            'offers' => [
                '@type' => 'Offer',
                'price' => '399',
                'priceCurrency' => 'MXN',
                'priceSpecification' => [
                    '@type' => 'UnitPriceSpecification',
                    'price' => '399',
                    'priceCurrency' => 'MXN',
                    'unitText' => 'MONTH',
                ],
            ],
            'url' => $siteUrl,
            'provider' => [
                '@type' => 'Organization',
                'name' => 'ContaboSaaS',
                'url' => $siteUrl,
            ],
        ];
    @endphp
    {!! json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    <link rel="icon" type="image/png" href="/images/favicon.png">

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --amber: #d97706;
            --amber-dk: #b45309;
            --amber-lt: #fef3c7;
            --amber-glow: rgba(217, 119, 6, 0.15);
            --slate-900: #0f172a;
            --slate-800: #1e293b;
            --slate-700: #334155;
            --slate-500: #64748b;
            --slate-200: #e2e8f0;
            --slate-100: #f1f5f9;
            --slate-50: #f8fafc;
            --white: #ffffff;
            --green-100: #dcfce7;
            --green-600: #16a34a;
            --radius: .75rem;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--slate-800);
            background: var(--white);
            line-height: 1.6;
            overflow-x: hidden;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        /* ── Animations ──────────────────────────────────────────────────────── */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(32px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-32px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(32px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.92);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 0 0 0 var(--amber-glow);
            }

            50% {
                box-shadow: 0 0 0 12px transparent;
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-6px);
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

        @keyframes badgePop {
            0% {
                opacity: 0;
                transform: scale(0.8) translateY(8px);
            }

            60% {
                transform: scale(1.05) translateY(-2px);
            }

            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes dotPulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.6;
                transform: scale(0.8);
            }
        }

        @keyframes counterUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Intersection Observer utility classes */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.65s cubic-bezier(0.22, 1, 0.36, 1),
                transform 0.65s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .animate-on-scroll.animate-left {
            transform: translateX(-28px);
        }

        .animate-on-scroll.animate-right {
            transform: translateX(28px);
        }

        .animate-on-scroll.animate-scale {
            transform: scale(0.93);
        }

        .animate-on-scroll.is-visible {
            opacity: 1;
            transform: none;
        }

        .animate-delay-1 {
            transition-delay: 0.1s;
        }

        .animate-delay-2 {
            transition-delay: 0.2s;
        }

        .animate-delay-3 {
            transition-delay: 0.3s;
        }

        .animate-delay-4 {
            transition-delay: 0.4s;
        }

        .animate-delay-5 {
            transition-delay: 0.5s;
        }

        .animate-delay-6 {
            transition-delay: 0.6s;
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
            background: rgba(255, 255, 255, .92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--slate-200);
            animation: navFadeIn 0.5s ease both;
            transition: box-shadow 0.3s ease;
        }

        nav.scrolled {
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07);
        }

        .logo {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--amber);
            letter-spacing: -.5px;
            transition: opacity 0.2s;
        }

        .logo:hover {
            opacity: 0.8;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .nav-links a {
            font-size: .9rem;
            color: var(--slate-700);
            font-weight: 500;
            position: relative;
            transition: color 0.2s;
        }

        .nav-links a:not(.btn)::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -2px;
            width: 0;
            height: 2px;
            background: var(--amber);
            border-radius: 99px;
            transition: width 0.25s ease;
        }

        .nav-links a:not(.btn):hover::after {
            width: 100%;
        }

        .nav-links a:hover {
            color: var(--amber);
        }

        /* Hamburger mobile menu */
        .nav-toggle {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            padding: 4px;
            background: none;
            border: none;
        }

        .nav-toggle span {
            display: block;
            width: 22px;
            height: 2px;
            background: var(--slate-700);
            border-radius: 99px;
            transition: transform 0.3s, opacity 0.3s;
        }

        .nav-toggle.open span:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
        }

        .nav-toggle.open span:nth-child(2) {
            opacity: 0;
        }

        .nav-toggle.open span:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
        }

        .mobile-menu {
            display: none;
            position: fixed;
            top: 65px;
            left: 0;
            right: 0;
            background: #fff;
            border-bottom: 1px solid var(--slate-200);
            padding: 1.5rem 2rem;
            z-index: 49;
            flex-direction: column;
            gap: 1rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            animation: fadeInUp 0.25s ease both;
        }

        .mobile-menu.open {
            display: flex;
        }

        .mobile-menu a {
            font-size: 1rem;
            font-weight: 600;
            color: var(--slate-700);
            padding: .5rem 0;
            border-bottom: 1px solid var(--slate-100);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .6rem 1.25rem;
            border-radius: .5rem;
            font-weight: 600;
            font-size: .9rem;
            cursor: pointer;
            border: none;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
        }

        .btn:active {
            transform: scale(.97);
        }

        .btn-primary {
            background: var(--amber);
            color: var(--white);
            box-shadow: 0 2px 8px rgba(217, 119, 6, 0.25);
        }

        .btn-primary:hover {
            background: var(--amber-dk);
            color: #ffffff !important;
            box-shadow: 0 4px 16px rgba(217, 119, 6, 0.4);
            transform: translateY(-1px);
        }

        .btn-outline {
            background: transparent;
            color: var(--slate-700);
            border: 1.5px solid var(--slate-200);
        }

        .btn-outline:hover {
            border-color: var(--amber);
            color: var(--amber);
            transform: translateY(-1px);
        }

        .btn-lg {
            padding: .875rem 2rem;
            font-size: 1rem;
            border-radius: var(--radius);
        }

        .btn-xl {
            padding: 1rem 2.25rem;
            font-size: 1.05rem;
            border-radius: var(--radius);
        }

        /* ── Hero ────────────────────────────────────────────────────────────── */
        .hero {
            padding: 7rem 2rem 5rem;
            text-align: center;
            background: linear-gradient(160deg, var(--slate-50) 0%, var(--amber-lt) 100%);
            position: relative;
            overflow: hidden;
        }

        /* Animated background orbs */
        .hero::before,
        .hero::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            pointer-events: none;
            z-index: 0;
        }

        .hero::before {
            width: 500px;
            height: 500px;
            background: rgba(217, 119, 6, 0.12);
            top: -100px;
            right: -100px;
            animation: float 8s ease-in-out infinite;
        }

        .hero::after {
            width: 400px;
            height: 400px;
            background: rgba(217, 119, 6, 0.08);
            bottom: -80px;
            left: -80px;
            animation: float 10s ease-in-out infinite reverse;
        }

        .hero>* {
            position: relative;
            z-index: 1;
        }

        .hero-badge {
            display: inline-block;
            margin-bottom: 1.5rem;
            padding: .4rem 1rem;
            border-radius: 99px;
            background: var(--amber-lt);
            border: 1px solid #fcd34d;
            font-size: .8rem;
            font-weight: 700;
            color: var(--amber-dk);
            letter-spacing: .3px;
            text-transform: uppercase;
            animation: badgePop 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
            animation-delay: 0.15s;
        }

        .hero h1 {
            font-size: clamp(2rem, 5vw, 3.6rem);
            font-weight: 800;
            line-height: 1.12;
            color: var(--slate-900);
            max-width: 780px;
            margin: 0 auto .85rem;
            letter-spacing: -.5px;
            animation: fadeInUp 0.8s cubic-bezier(0.22, 1, 0.36, 1) both;
            animation-delay: 0.25s;
        }

        .hero h1 span {
            color: var(--amber);
            background: linear-gradient(90deg, #d97706, #f59e0b, #d97706);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer 3s linear infinite;
        }

        .hero p {
            font-size: 1.15rem;
            color: var(--slate-500);
            max-width: 580px;
            margin: 0 auto 2.5rem;
            animation: fadeInUp 0.8s cubic-bezier(0.22, 1, 0.36, 1) both;
            animation-delay: 0.35s;
        }

        .hero-ctas {
            display: flex;
            gap: .75rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 0.8s cubic-bezier(0.22, 1, 0.36, 1) both;
            animation-delay: 0.45s;
        }

        .hero-note {
            margin-top: 1rem;
            font-size: .82rem;
            color: var(--slate-500);
            animation: fadeIn 1s ease both;
            animation-delay: 0.65s;
        }

        /* ── Stats bar ───────────────────────────────────────────────────────── */
        .stats-bar {
            display: flex;
            justify-content: center;
            gap: 3rem;
            flex-wrap: wrap;
            margin-top: 3.5rem;
            padding-top: 2.5rem;
            border-top: 1px solid rgba(217, 119, 6, 0.15);
            animation: fadeIn 1s ease both;
            animation-delay: 0.75s;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--amber);
            line-height: 1;
            letter-spacing: -1px;
        }

        .stat-label {
            font-size: .8rem;
            color: var(--slate-500);
            margin-top: .25rem;
            font-weight: 500;
        }

        /* ── Logos / Social proof ─────────────────────────────────────────────── */
        .social-proof {
            padding: 2.5rem 2rem;
            text-align: center;
            border-bottom: 1px solid var(--slate-200);
            background: var(--white);
        }

        .social-proof p {
            font-size: .85rem;
            color: var(--slate-500);
            margin-bottom: 1.5rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .regime-tags {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: .5rem;
        }

        .regime-tag {
            padding: .4rem .85rem;
            border-radius: 99px;
            background: var(--slate-100);
            border: 1px solid var(--slate-200);
            font-size: .82rem;
            font-weight: 600;
            color: var(--slate-700);
            transition: background 0.2s, border-color 0.2s, color 0.2s, transform 0.2s;
            cursor: default;
        }

        .regime-tag:hover {
            background: var(--amber-lt);
            border-color: #fcd34d;
            color: var(--amber-dk);
            transform: translateY(-2px);
        }

        /* ── Section wrapper ─────────────────────────────────────────────────── */
        section {
            padding: 5rem 2rem;
        }

        .section-inner {
            max-width: 1100px;
            margin: 0 auto;
        }

        .section-label {
            display: inline-block;
            margin-bottom: .75rem;
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: var(--amber);
        }

        .section-title {
            font-size: clamp(1.6rem, 3vw, 2.25rem);
            font-weight: 800;
            color: var(--slate-900);
            line-height: 1.2;
            margin-bottom: .75rem;
        }

        .section-sub {
            font-size: 1rem;
            color: var(--slate-500);
            max-width: 560px;
        }

        /* ── Features grid ───────────────────────────────────────────────────── */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 3rem;
        }

        .feature-card {
            padding: 2rem;
            border-radius: var(--radius);
            border: 1.5px solid var(--slate-200);
            background: var(--white);
            transition: border-color 0.3s, box-shadow 0.3s, transform 0.3s;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(217, 119, 6, 0.04) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .feature-card:hover {
            border-color: #fcd34d;
            box-shadow: 0 8px 32px rgba(217, 119, 6, 0.12);
            transform: translateY(-4px);
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-icon {
            width: 52px;
            height: 52px;
            border-radius: .75rem;
            background: var(--amber-lt);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.25rem;
            transition: transform 0.3s;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(-3deg);
        }

        .feature-card h3 {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: .5rem;
            color: var(--slate-900);
        }

        .feature-card p {
            font-size: .9rem;
            color: var(--slate-500);
            line-height: 1.65;
        }

        /* ── Obligations section ─────────────────────────────────────────────── */
        .obligations-section {
            background: var(--slate-50);
        }

        .obligations-split {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            margin-top: 3rem;
        }

        .obligation-list {
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }

        .obligation-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            border-radius: .6rem;
            background: var(--white);
            border: 1.5px solid var(--slate-200);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .obligation-item:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        }

        .ob-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .ob-dot.pending {
            background: #f59e0b;
            animation: dotPulse 2s ease-in-out infinite;
        }

        .ob-dot.presented {
            background: var(--green-600);
        }

        .ob-dot.overdue {
            background: #ef4444;
            animation: dotPulse 1.5s ease-in-out infinite;
        }

        .ob-dot.na {
            background: var(--slate-500);
        }

        .obligation-item strong {
            font-size: .9rem;
            color: var(--slate-800);
        }

        .obligation-item span {
            font-size: .8rem;
            color: var(--slate-500);
            margin-left: auto;
            white-space: nowrap;
        }

        .obligations-text .section-sub {
            margin-bottom: 1.5rem;
        }

        .check-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: .6rem;
            margin-top: 1.5rem;
        }

        .check-list li {
            font-size: .92rem;
            color: var(--slate-700);
            display: flex;
            gap: .6rem;
            align-items: flex-start;
        }

        .check-list li::before {
            content: '✓';
            color: var(--green-600);
            font-weight: 700;
            flex-shrink: 0;
            margin-top: .05rem;
        }

        /* ── Pricing ─────────────────────────────────────────────────────────── */
        .pricing-section {
            background: var(--white);
            text-align: center;
        }

        .pricing-card {
            max-width: 440px;
            margin: 3rem auto 0;
            border: 2px solid var(--amber);
            border-radius: 1.25rem;
            overflow: hidden;
            box-shadow: 0 8px 40px rgba(217, 119, 6, .15);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .pricing-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 60px rgba(217, 119, 6, .25);
        }

        .pricing-header {
            background: linear-gradient(135deg, var(--amber) 0%, #f59e0b 100%);
            color: var(--white);
            padding: 2.25rem 2rem 1.75rem;
            position: relative;
            overflow: hidden;
        }

        .pricing-header::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            top: -60px;
            right: -60px;
        }

        .pricing-header h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: .5rem;
            opacity: .9;
        }

        .pricing-price {
            font-size: 3.2rem;
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1;
            position: relative;
        }

        .pricing-price sup {
            font-size: 1.2rem;
            font-weight: 700;
            vertical-align: top;
            margin-top: .5rem;
        }

        .pricing-price small {
            font-size: 1rem;
            font-weight: 400;
            opacity: .8;
        }

        .pricing-iva {
            font-size: .78rem;
            opacity: .7;
            margin-top: .35rem;
        }

        .pricing-body {
            padding: 2rem;
            background: var(--white);
        }

        .pricing-features {
            list-style: none;
            text-align: left;
            margin-bottom: 2rem;
            display: flex;
            flex-direction: column;
            gap: .65rem;
        }

        .pricing-features li {
            font-size: .92rem;
            color: var(--slate-700);
            display: flex;
            gap: .6rem;
            align-items: center;
        }

        .pricing-features li::before {
            content: '✓';
            color: var(--green-600);
            font-weight: 700;
            flex-shrink: 0;
        }

        .pricing-trial-note {
            font-size: .82rem;
            color: var(--slate-500);
            margin-top: .75rem;
        }

        /* ── CTA final ───────────────────────────────────────────────────────── */
        .cta-section {
            background: linear-gradient(135deg, var(--slate-900) 0%, #1a2744 100%);
            color: var(--white);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: rgba(217, 119, 6, 0.06);
            top: -200px;
            right: -100px;
            pointer-events: none;
        }

        .cta-section>* {
            position: relative;
            z-index: 1;
        }

        .cta-section .section-title {
            color: var(--white);
        }

        .cta-section p {
            color: #94a3b8;
            margin: .75rem auto 2rem;
            max-width: 480px;
        }

        .cta-section .btn-primary {
            animation: pulse-glow 3s ease-in-out infinite;
        }

        /* ── Footer ──────────────────────────────────────────────────────────── */
        footer {
            padding: 2.5rem 2rem;
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
            margin-bottom: .75rem;
        }

        .footer-links a {
            color: var(--slate-500);
            font-size: .82rem;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: var(--amber);
        }

        .footer-sep {
            color: var(--slate-200);
        }

        /* ── Responsive ──────────────────────────────────────────────────────── */
        @media (max-width: 768px) {
            nav {
                padding: .875rem 1.25rem;
            }

            .nav-links .nav-hide {
                display: none;
            }

            .nav-toggle {
                display: flex;
            }

            .hero {
                padding: 5rem 1.25rem 3.5rem;
            }

            .stats-bar {
                gap: 2rem;
            }

            section {
                padding: 3.5rem 1.25rem;
            }

            .obligations-split {
                grid-template-columns: 1fr;
                gap: 2.5rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .obligation-item strong {
                font-size: .82rem;
            }
        }

        @media (max-width: 480px) {
            .hero h1 {
                font-size: 1.85rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .hero-ctas {
                flex-direction: column;
                align-items: center;
            }

            .hero-ctas .btn-xl {
                width: 100%;
                max-width: 320px;
            }

            .stats-bar {
                gap: 1.5rem;
            }

            .stat-number {
                font-size: 1.6rem;
            }

            .pricing-card {
                margin-left: .5rem;
                margin-right: .5rem;
            }
        }
    </style>
</head>

<body>

    <!-- ── Navegación ──────────────────────────────────────────────────────────── -->
    <nav id="main-nav">
        <div class="logo">
            <a href="/">
                <img src="/images/contabo.png" alt="ContaboSaaS — Software contable para México"
                    style="height: 4rem; display: block;">
            </a>
        </div>
        <div class="nav-links">
            <a href="#features" class="nav-hide">Funciones</a>
            <a href="#pricing" class="nav-hide">Precios</a>
            <a href="/admin/login" class="btn btn-outline">Iniciar sesión</a>
            <a href="/register" class="btn btn-primary">Probar gratis</a>
        </div>
        <button class="nav-toggle" id="nav-toggle" aria-label="Abrir menú" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
    </nav>

    <!-- Mobile menu -->
    <div class="mobile-menu" id="mobile-menu" role="navigation" aria-label="Menú móvil">
        <a href="#features" onclick="closeMobileMenu()">Funciones</a>
        <a href="#pricing" onclick="closeMobileMenu()">Precios</a>
        <a href="/admin/login">Iniciar sesión</a>
        <a href="/register" class="btn btn-primary" style="text-align:center; margin-top:.5rem">Probar gratis — 14
            días</a>
    </div>

    <!-- ── Hero ───────────────────────────────────────────────────────────────── -->
    <section class="hero" aria-label="Propuesta de valor principal">
        <div class="hero-badge">Hecho para México · Regímenes SAT incluidos</div>
        <h1>El panel que los <span>contadores mexicanos</span> necesitaban</h1>
        <p>Gestiona todos tus clientes, sus facturas CFDI y sus obligaciones fiscales desde un solo lugar. Sin hojas de
            cálculo, sin caos.</p>
        <div class="hero-ctas">
            <a href="/register" class="btn btn-primary btn-xl">Empezar gratis — 14 días</a>
            <a href="/admin/login" class="btn btn-outline btn-xl">Ya tengo cuenta</a>
        </div>
        <p class="hero-note">Sin tarjeta de crédito para el período de prueba.</p>

        <div class="stats-bar" role="list" aria-label="Estadísticas del producto">
            <div class="stat-item" role="listitem">
                <div class="stat-number">8+</div>
                <div class="stat-label">Regímenes SAT</div>
            </div>
            <div class="stat-item" role="listitem">
                <div class="stat-number">14</div>
                <div class="stat-label">Días gratis</div>
            </div>
            <div class="stat-item" role="listitem">
                <div class="stat-number">∞</div>
                <div class="stat-label">Clientes incluidos</div>
            </div>
            <div class="stat-item" role="listitem">
                <div class="stat-number">100%</div>
                <div class="stat-label">SAT compliance</div>
            </div>
        </div>
    </section>

    <!-- ── Regímenes ──────────────────────────────────────────────────────────── -->
    <div class="social-proof" aria-label="Regímenes fiscales soportados">
        <p>Regímenes fiscales soportados</p>
        <div class="regime-tags">
            <span class="regime-tag animate-on-scroll animate-delay-1">601 · General PM</span>
            <span class="regime-tag animate-on-scroll animate-delay-2">606 · Arrendamiento</span>
            <span class="regime-tag animate-on-scroll animate-delay-3">612 · Act. Empresariales PF</span>
            <span class="regime-tag animate-on-scroll animate-delay-4">621 · RIF</span>
            <span class="regime-tag animate-on-scroll animate-delay-5">625 · Plataformas Tech</span>
            <span class="regime-tag animate-on-scroll animate-delay-6">626 · RESICO</span>
            <span class="regime-tag animate-on-scroll animate-delay-1">605 · Sueldos y Salarios</span>
            <span class="regime-tag animate-on-scroll animate-delay-2">603 · Fines no Lucrativos</span>
        </div>
    </div>

    <!-- ── Características ────────────────────────────────────────────────────── -->
    <section id="features" aria-labelledby="features-title">
        <div class="section-inner">
            <span class="section-label animate-on-scroll">Funciones</span>
            <h2 class="section-title animate-on-scroll animate-delay-1" id="features-title">
                Todo lo que tu despacho necesita en un solo panel
            </h2>
            <p class="section-sub animate-on-scroll animate-delay-2">
                Diseñado con los flujos reales de un contador mexicano, no con funciones genéricas.
            </p>

            <div class="features-grid">
                <div class="feature-card animate-on-scroll animate-delay-1">
                    <div class="feature-icon" aria-hidden="true">🗂️</div>
                    <h3>Expediente de clientes</h3>
                    <p>Guarda la información fiscal de cada cliente: RFC, régimen SAT, e.firma (CER/KEY), notas internas
                        y documentos. Todo en un solo expediente.</p>
                </div>
                <div class="feature-card animate-on-scroll animate-delay-2">
                    <div class="feature-icon" aria-hidden="true">🧾</div>
                    <h3>Facturas CFDI</h3>
                    <p>Importa XMLs directamente. El sistema extrae automáticamente UUID, folio, emisor, receptor, monto
                        e impuestos. Descarga el PDF cuando lo necesites.</p>
                </div>
                <div class="feature-card animate-on-scroll animate-delay-3">
                    <div class="feature-icon" aria-hidden="true">📅</div>
                    <h3>Obligaciones fiscales</h3>
                    <p>Genera automáticamente las obligaciones según el régimen SAT de cada cliente. ISR, IVA, DIOT,
                        IMSS y declaraciones anuales con fechas límite correctas.</p>
                </div>
                <div class="feature-card animate-on-scroll animate-delay-4">
                    <div class="feature-icon" aria-hidden="true">📊</div>
                    <h3>Dashboard con reportes</h3>
                    <p>Ve de un vistazo cuántas obligaciones están pendientes, cuántas vencidas, y cuántas facturas
                        entraron este mes. Gráfica por estatus y período.</p>
                </div>
                <div class="feature-card animate-on-scroll animate-delay-5">
                    <div class="feature-icon" aria-hidden="true">👥</div>
                    <h3>Equipo de trabajo</h3>
                    <p>Invita capturistas y asistentes a tu panel. Ellos pueden capturar y editar; tú controlas quién
                        puede eliminar o ver configuración.</p>
                </div>
                <div class="feature-card animate-on-scroll animate-delay-6">
                    <div class="feature-icon" aria-hidden="true">🔒</div>
                    <h3>Acceso por roles</h3>
                    <p>Tres niveles de acceso: Administrador, Capturista y Solo lectura. Cada cliente ve solo sus datos;
                        cada rol solo lo que le corresponde.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Obligaciones ────────────────────────────────────────────────────────── -->
    <section class="obligations-section" aria-labelledby="obligations-title">
        <div class="section-inner">
            <div class="obligations-split">
                <div class="obligation-list" role="list" aria-label="Ejemplo de obligaciones fiscales">
                    <div class="obligation-item animate-on-scroll animate-delay-1" role="listitem">
                        <div class="ob-dot pending" aria-label="Pendiente"></div>
                        <strong>ISR Mensual · Empresa ABC · Mayo 2025</strong>
                        <span>Vence 17 jun</span>
                    </div>
                    <div class="obligation-item animate-on-scroll animate-delay-2" role="listitem">
                        <div class="ob-dot presented" aria-label="Presentada"></div>
                        <strong>IVA Mensual · Arrendador López · Abr 2025</strong>
                        <span>Presentada</span>
                    </div>
                    <div class="obligation-item animate-on-scroll animate-delay-3" role="listitem">
                        <div class="ob-dot overdue" aria-label="Vencida"></div>
                        <strong>DIOT · Servicios Tech SA · Mar 2025</strong>
                        <span>Vencida</span>
                    </div>
                    <div class="obligation-item animate-on-scroll animate-delay-4" role="listitem">
                        <div class="ob-dot na" aria-label="No aplica"></div>
                        <strong>IMSS Bimestral · García PF · Ene-Feb 2025</strong>
                        <span>No aplica</span>
                    </div>
                    <div class="obligation-item animate-on-scroll animate-delay-5" role="listitem">
                        <div class="ob-dot pending" aria-label="Pendiente"></div>
                        <strong>Declaración Anual PM · Grupo Norte · 2024</strong>
                        <span>Vence 31 mar</span>
                    </div>
                </div>
                <div class="obligations-text animate-on-scroll animate-right">
                    <span class="section-label">Calendario fiscal</span>
                    <h2 class="section-title" id="obligations-title">Nunca más una obligación vencida</h2>
                    <p class="section-sub">El sistema genera automáticamente el calendario fiscal de cada cliente según
                        su régimen SAT, con las fechas límite correctas del SAT.</p>
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
    <section id="pricing" class="pricing-section" aria-labelledby="pricing-title">
        <div class="section-inner">
            <span class="section-label animate-on-scroll">Precios</span>
            <h2 class="section-title animate-on-scroll animate-delay-1" id="pricing-title">Un plan. Sin sorpresas.
            </h2>
            <p class="section-sub animate-on-scroll animate-delay-2" style="margin: 0 auto;">
                Sin límites de clientes, sin funciones bloqueadas, sin planes confusos. Todo incluido desde el primer
                día.
            </p>

            <div class="pricing-card animate-on-scroll animate-scale animate-delay-2">
                <div class="pricing-header">
                    <h3>Plan Despacho</h3>
                    <div class="pricing-price">
                        <sup>$</sup>399<small> MXN/mes</small>
                    </div>
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
    <section class="cta-section" aria-label="Llamada a la acción final">
        <div class="section-inner">
            <h2 class="section-title animate-on-scroll">Empieza a ordenar tu despacho hoy</h2>
            <p class="animate-on-scroll animate-delay-1">14 días gratis, sin tarjeta. Si no te convence, no pagas nada.
            </p>
            <a href="/register" class="btn btn-primary btn-xl animate-on-scroll animate-delay-2">Crear cuenta
                gratis</a>
        </div>
    </section>

    <!-- ── Footer ─────────────────────────────────────────────────────────────── -->
    <footer>
        <div class="footer-links">
            <a href="{{ route('legal.privacy') }}">Aviso de Privacidad</a>
            <span class="footer-sep">·</span>
            <a href="{{ route('legal.terms') }}">Términos y Condiciones</a>
            <span class="footer-sep">·</span>
            <a href="/admin/login">Iniciar sesión</a>
        </div>
        <p>© {{ date('Y') }} ContaboSaaS · Software de contabilidad para despachos mexicanos</p>
    </footer>

    <script>
        // ── Nav scroll effect ────────────────────────────────────────────────────
        const nav = document.getElementById('main-nav');
        window.addEventListener('scroll', () => {
            nav.classList.toggle('scrolled', window.scrollY > 20);
        }, {
            passive: true
        });

        // ── Mobile menu toggle ───────────────────────────────────────────────────
        const toggle = document.getElementById('nav-toggle');
        const mobileMenu = document.getElementById('mobile-menu');

        toggle.addEventListener('click', () => {
            const isOpen = mobileMenu.classList.toggle('open');
            toggle.classList.toggle('open', isOpen);
            toggle.setAttribute('aria-expanded', isOpen);
        });

        function closeMobileMenu() {
            mobileMenu.classList.remove('open');
            toggle.classList.remove('open');
            toggle.setAttribute('aria-expanded', false);
        }

        // Close mobile menu on outside click
        document.addEventListener('click', (e) => {
            if (!nav.contains(e.target) && !mobileMenu.contains(e.target)) {
                closeMobileMenu();
            }
        });

        // ── Intersection Observer for scroll animations ──────────────────────────
        const animatedEls = document.querySelectorAll('.animate-on-scroll');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.12,
            rootMargin: '0px 0px -40px 0px'
        });

        animatedEls.forEach(el => observer.observe(el));

        // ── Smooth scroll for nav links ──────────────────────────────────────────
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>

</body>

</html>
