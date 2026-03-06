<x-emails.layout title="Pago recibido" header-color="#16a34a" :recipient-email="$user->email">
    <h2>Hola, {{ $user->name }}</h2>
    <p>Hemos recibido tu pago correctamente. Tu suscripción a CONTABO sigue activa sin interrupciones.</p>

    <div class="info-box" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
        <p style="color: #166534;">
            ✅ <strong style="color: #14532d;">Pago procesado exitosamente</strong>
        </p>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin: 24px 0; font-size: 14px;">
        <tbody>
            <tr>
                <td
                    style="border: 1px solid #e5e7eb; padding: 12px 16px; color: #6b7280; font-weight: 600; background: #f9fafb; width: 40%;">
                    Fecha</td>
                <td style="border: 1px solid #e5e7eb; padding: 12px 16px; color: #111827;">{{ $invoiceDate }}</td>
            </tr>
            <tr>
                <td
                    style="border: 1px solid #e5e7eb; padding: 12px 16px; color: #6b7280; font-weight: 600; background: #f9fafb;">
                    Monto</td>
                <td style="border: 1px solid #e5e7eb; padding: 12px 16px; color: #111827; font-weight: 700;">
                    ${{ number_format($amountInCents / 100, 2) }} MXN</td>
            </tr>
            <tr>
                <td
                    style="border: 1px solid #e5e7eb; padding: 12px 16px; color: #6b7280; font-weight: 600; background: #f9fafb;">
                    Estado</td>
                <td style="border: 1px solid #e5e7eb; padding: 12px 16px;">
                    <span
                        style="display: inline-block; background: #dcfce7; color: #166534; padding: 2px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600;">Pagado</span>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="btn-wrap">
        <a href="{{ config('app.url') }}/admin/billing" class="btn" style="background: #d97706; color: #ffffff;">Ver
            mi suscripción →</a>
    </div>

    <hr class="divider">
    <p style="font-size: 14px;">Si tienes alguna pregunta sobre este cobro, responde a este correo.</p>
    <p style="font-size: 14px;">— El equipo de CONTABO</p>
</x-emails.layout>
