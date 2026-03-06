<x-emails.layout title="Obligaciones fiscales por vencer" header-color="#dc2626" :recipient-email="$user->email">
    <h2>Hola, {{ $user->name }}</h2>
    <p>Tienes <strong>{{ $obligations->count() }} obligación(es) fiscal(es)</strong> que vencen en
        <strong>{{ $daysUntilDue }} día(s)</strong>.</p>

    <div class="info-box" style="background: #fef2f2; border: 1px solid #fecaca;">
        <p style="color: #991b1b;">
            📅 Fecha límite: <strong
                style="color: #7f1d1d;">{{ now()->addDays($daysUntilDue)->translatedFormat('d \d\e F \d\e Y') }}</strong>
        </p>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin: 24px 0; font-size: 14px;">
        <thead>
            <tr>
                <th
                    style="background: #f9fafb; border: 1px solid #e5e7eb; padding: 10px 14px; text-align: left; font-weight: 600; color: #374151;">
                    Cliente</th>
                <th
                    style="background: #f9fafb; border: 1px solid #e5e7eb; padding: 10px 14px; text-align: left; font-weight: 600; color: #374151;">
                    Obligación</th>
                <th
                    style="background: #f9fafb; border: 1px solid #e5e7eb; padding: 10px 14px; text-align: left; font-weight: 600; color: #374151;">
                    Período</th>
                <th
                    style="background: #f9fafb; border: 1px solid #e5e7eb; padding: 10px 14px; text-align: left; font-weight: 600; color: #374151;">
                    Vence</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($obligations as $i => $obligation)
                <tr>
                    <td
                        style="border: 1px solid #e5e7eb; padding: 10px 14px; color: #4b5563; {{ $i % 2 === 1 ? 'background: #f9fafb;' : '' }}">
                        {{ $obligation->client->name }}</td>
                    <td
                        style="border: 1px solid #e5e7eb; padding: 10px 14px; color: #4b5563; {{ $i % 2 === 1 ? 'background: #f9fafb;' : '' }}">
                        {{ $obligation->type->label() }}</td>
                    <td
                        style="border: 1px solid #e5e7eb; padding: 10px 14px; color: #4b5563; {{ $i % 2 === 1 ? 'background: #f9fafb;' : '' }}">
                        {{ $obligation->periodLabel() }}</td>
                    <td
                        style="border: 1px solid #e5e7eb; padding: 10px 14px; color: #4b5563; {{ $i % 2 === 1 ? 'background: #f9fafb;' : '' }}">
                        {{ $obligation->due_date->translatedFormat('d M Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Accede a CONTABO para marcarlas como presentadas una vez que las hayas enviado al SAT.</p>

    <div class="btn-wrap">
        <a href="{{ config('app.url') }}/admin/fiscal-obligations" class="btn"
            style="background: #dc2626; color: #ffffff;">Ver obligaciones →</a>
    </div>

    <hr class="divider">
    <p style="font-size: 14px;">— El equipo de CONTABO</p>
</x-emails.layout>
