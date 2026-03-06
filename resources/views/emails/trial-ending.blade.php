<x-emails.layout title="Tu prueba vence pronto" :recipient-email="$user->email">
    <h2>Hola, {{ $user->name }}</h2>
    <p>Te recordamos que tu prueba gratuita de CONTABO está por vencer.</p>

    <div class="info-box" style="background: #fef2f2; border: 1px solid #fecaca;">
        <p style="color: #991b1b;">
            ⏳ Tu prueba vence el <strong
                style="color: #7f1d1d;">{{ $user->trial_ends_at->translatedFormat('d \d\e F \d\e Y') }}</strong>.<br>
            A partir de entonces, necesitarás una suscripción activa para seguir usando la plataforma.
        </p>
    </div>

    <p>Para continuar sin interrupciones, activa tu suscripción ahora. El proceso toma menos de 2 minutos.</p>

    <div class="btn-wrap">
        <a href="{{ config('app.url') }}/subscription" class="btn" style="background: #d97706; color: #ffffff;">Activar
            mi suscripción →</a>
    </div>

    <hr class="divider">
    <p style="font-size: 14px;">Si tienes alguna pregunta, responde a este correo y con gusto te ayudamos.</p>
    <p style="font-size: 14px;">— El equipo de CONTABO</p>
</x-emails.layout>
