<?php

namespace App\Filament\Widgets;

use App\Enums\ObligationStatus;
use App\Filament\Pages\Dashboard;
use App\Filament\Resources\FiscalObligations\FiscalObligationResource;
use App\Mail\ObligationPresentedMail;
use App\Models\FiscalObligation;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ProximasObligacionesWidget extends BaseWidget
{
    protected static ?string $heading = 'Urgente — Vencidas y por vencer esta semana';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $clientId = Dashboard::getActiveClientId();

        return $table
            ->query(
                FiscalObligation::query()
                    ->whereIn('status', [ObligationStatus::Pending, ObligationStatus::Overdue])
                    ->whereBetween('due_date', [
                        now()->subDays(30)->toDateString(),
                        now()->addDays(7)->toDateString(),
                    ])
                    ->when($clientId, fn ($q) => $q->where('client_id', $clientId))
                    ->orderBy('due_date', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('days_remaining')
                    ->label('Estado')
                    ->getStateUsing(function (FiscalObligation $record): string {
                        $days = (int) Carbon::today()->diffInDays($record->due_date, false);

                        if ($days < 0) {
                            return abs($days) === 1 ? 'Vencida hace 1 día' : 'Vencida hace '.abs($days).' días';
                        }

                        if ($days === 0) {
                            return 'Vence hoy';
                        }

                        return $days === 1 ? 'Mañana' : "En {$days} días";
                    })
                    ->badge()
                    ->color(function (FiscalObligation $record): string {
                        $days = (int) Carbon::today()->diffInDays($record->due_date, false);

                        if ($days < 0) {
                            return 'danger';
                        }

                        if ($days <= 2) {
                            return 'warning';
                        }

                        return 'info';
                    }),

                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('type')
                    ->label('Obligación')
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('period_label')
                    ->label('Período')
                    ->getStateUsing(fn (FiscalObligation $record): string => $record->periodLabel()),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Fecha límite')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->actions([
                Action::make('mark_presented')
                    ->label('Marcar presentada')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        DatePicker::make('presented_at')
                            ->label('Fecha de presentación')
                            ->required()
                            ->default(now())
                            ->displayFormat('d/m/Y'),
                        TextInput::make('reference')
                            ->label('Número de acuse / referencia SAT')
                            ->maxLength(100),
                        FileUpload::make('acuse_pdf_path')
                            ->label('PDF del acuse SAT (opcional)')
                            ->disk('local')
                            ->directory(fn (FiscalObligation $record): string => 'fiscal-obligations/acuses/'.$record->id)
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240)
                            ->nullable(),
                    ])
                    ->action(function (FiscalObligation $record, array $data): void {
                        if (! empty($data['acuse_pdf_path']) && $record->acuse_pdf_path && $record->acuse_pdf_path !== $data['acuse_pdf_path']) {
                            Storage::disk('local')->delete($record->acuse_pdf_path);
                        }

                        $record->update([
                            'status' => ObligationStatus::Presented,
                            'presented_at' => $data['presented_at'],
                            'reference' => $data['reference'] ?? null,
                            'acuse_pdf_path' => $data['acuse_pdf_path'] ?? $record->acuse_pdf_path,
                        ]);

                        $client = $record->client;

                        if ($client && $client->email) {
                            Mail::to($client->email)->queue(new ObligationPresentedMail($record));
                        }
                    }),

                Action::make('editar')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil-square')
                    ->color('gray')
                    ->url(fn (FiscalObligation $record): string => FiscalObligationResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false)
            ->emptyStateHeading('Todo al corriente')
            ->emptyStateDescription('No hay obligaciones vencidas ni con vencimiento en los próximos 7 días.')
            ->emptyStateIcon('heroicon-o-check-badge');
    }
}
