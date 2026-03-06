<?php

namespace App\Filament\Widgets;

use App\Enums\ObligationStatus;
use App\Filament\Resources\Clients\ClientResource;
use App\Models\Client;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ClientesEnRiesgoWidget extends BaseWidget
{
    protected static ?string $heading = 'Clientes en riesgo (con obligaciones vencidas)';

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Client::query()
                    ->whereHas('fiscalObligations', fn ($q) => $q->where('status', ObligationStatus::Overdue))
                    ->withCount([
                        'fiscalObligations as overdue_count' => fn ($q) => $q->where('status', ObligationStatus::Overdue),
                    ])
                    ->orderByDesc('overdue_count')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('rfc')
                    ->label('RFC')
                    ->searchable(),

                Tables\Columns\TextColumn::make('overdue_count')
                    ->label('Obligaciones vencidas')
                    ->badge()
                    ->color('danger')
                    ->sortable(),
            ])
            ->actions([
                Action::make('ver')
                    ->label('Ver cliente')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Client $record): string => ClientResource::getUrl('view', ['record' => $record])),
            ])
            ->emptyStateHeading('Sin clientes en riesgo')
            ->emptyStateDescription('Todos los clientes están al corriente con sus obligaciones fiscales.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->paginated(false);
    }
}
