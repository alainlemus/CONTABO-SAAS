<x-emails.layout title="Obligación fiscal presentada" header-color="#16a34a" :recipient-email="$obligation->client->user->email">
    <h2>Hola, {{ $obligation->client->name }}</h2>
    <p>Tu contador ha presentado la siguiente obligación fiscal ante el SAT:</p>

    <div class="info-box" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
        <p style="color: #166534;">✅ Estado: <strong style="color: #14532d;">Presentada correctamente</strong></p>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin: 24px 0; font-size: 14px;">
        <thead>
            <tr>
                <th style="background: #f9fafb; border: 1px solid #e5e7eb; padding: 10px 14px; text-align: left; font-weight: 600; color: #374151;">Detalle</th>
                <th style="background: #f9fafb; border: 1px solid #e5e7eb; padding: 10px 14px; text-align: left; font-weight: 600; color: #374151;">Información</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #e5e7eb; padding: 10px 14px; color: #4b5563;"><strong>Obligación</strong></td>
                <td style="border: 1px solid #e5e7eb; padding: 10px 14px; color: #4b5563;">{{ $obligation->type->label() }}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #e5e7eb; padding: 10px 14px; color: #4b5563; background: #f9fafb;"><strong>Período</strong></td>
                <td style="border: 1px solid #e5e7eb; padding: 10px 14px; color: #4b5563; background: #f9fafb;">{{ $obligation->periodLabel() }}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #e5e7eb; padding: 10px 14px; color: #4b5563;"><strong>Fecha de vencimiento</strong></td>
                <td style="border: 1px solid #e5e7eb; padding: 10px 14px; color: #4b5563;">{{ $obligation->due_date->translatedFormat('d \d\e F \d\e Y') }}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #e5e7eb; padding: 10px 14px; color: #4b5563; background: #f9fafb;"><strong>Fecha de presentación</strong></td>
                <td style="border: 1px solid #e5e7eb; padding: 10px 14px; color: #4b5563; background: #f9fafb;">{{ $obligation->presented_at->translatedFormat('d \d\e F \d\e Y') }}</td>
            </tr>
            @if ($obligation->reference)
            <tr>
                <td style="border: 1px solid #e5e7eb; padding: 10px 14px; color: #4b5563;"><strong>Número de acuse / referencia SAT</strong></td>
                <td style="border: 1px solid #e5e7eb; padding: 10px 14px; color: #4b5563;">{{ $obligation->reference }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <hr class="divider">
    <p style="font-size: 14px;">Si tienes alguna duda sobre esta presentación, comunícate con tu contador.</p>
    <p style="font-size: 14px;">— El equipo de CONTABO</p>
</x-emails.layout>
