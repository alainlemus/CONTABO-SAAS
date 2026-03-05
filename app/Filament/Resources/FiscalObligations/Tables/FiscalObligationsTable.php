<?php

namespace App\Filament\Resources\FiscalObligations\Tables;

use App\Enums\ObligationStatus;
use App\Enums\ObligationType;
use App\Models\Client;
use App\Models\FiscalObligation;
use App\Services\FiscalObligationGenerator;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class FiscalObligationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('type')
                    ->label('Obligación')
                    ->formatStateUsing(fn (ObligationType $state): string => $state->label())
                    ->badge()
                    ->color('info'),

                TextColumn::make('period')
                    ->label('Período')
                    ->state(fn (FiscalObligation $record): string => $record->periodLabel())
                    ->sortable(query: fn ($query, string $direction) => $query
                        ->orderBy('period_year', $direction)
                        ->orderBy('period_month', $direction)),

                TextColumn::make('due_date')
                    ->label('Vence')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn (FiscalObligation $record): string => $record->isOverdue() ? 'danger' : 'gray'),

                TextColumn::make('status')
                    ->label('Estatus')
                    ->badge()
                    ->formatStateUsing(fn (ObligationStatus $state): string => $state->label())
                    ->color(fn (ObligationStatus $state): string => $state->color()),

                TextColumn::make('presented_at')
                    ->label('Presentada')
                    ->date('d/m/Y')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('reference')
                    ->label('Acuse')
                    ->placeholder('—')
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('acuse_pdf_path')
                    ->label('PDF')
                    ->icon(fn (?string $state): string => $state
                        ? 'heroicon-o-document-arrow-down'
                        : 'heroicon-o-document')
                    ->color(fn (?string $state): string => $state ? 'success' : 'gray')
                    ->tooltip(fn (?string $state): string => $state
                        ? 'Descargar PDF del acuse'
                        : 'Sin acuse PDF')
                    ->action(
                        Action::make('download_acuse_from_column')
                            ->action(function (FiscalObligation $record): mixed {
                                if (! $record->acusePdfExists()) {
                                    return null;
                                }

                                return redirect()->route('fiscal-obligations.download.acuse', $record);
                            })
                    ),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estatus')
                    ->options(ObligationStatus::options()),

                SelectFilter::make('type')
                    ->label('Tipo de obligación')
                    ->options(ObligationType::options()),

                SelectFilter::make('client')
                    ->label('Cliente')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                // Acción rápida: marcar como presentada
                Action::make('mark_presented')
                    ->label('Marcar presentada')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (FiscalObligation $record): bool => $record->status === ObligationStatus::Pending || $record->status === ObligationStatus::Overdue)
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
                        // Eliminar PDF previo si se subió uno nuevo y existía uno anterior
                        if (! empty($data['acuse_pdf_path']) && $record->acuse_pdf_path && $record->acuse_pdf_path !== $data['acuse_pdf_path']) {
                            Storage::disk('local')->delete($record->acuse_pdf_path);
                        }

                        $record->update([
                            'status' => ObligationStatus::Presented,
                            'presented_at' => $data['presented_at'],
                            'reference' => $data['reference'] ?? null,
                            'acuse_pdf_path' => $data['acuse_pdf_path'] ?? $record->acuse_pdf_path,
                        ]);
                    }),



                EditAction::make(),
            ])
            ->toolbarActions([
                // Acción de header: generar obligaciones del mes actual para todos los clientes
                Action::make('generate_current_month')
                    ->label('Generar obligaciones del mes')
                    ->icon('heroicon-o-sparkles')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Generar obligaciones fiscales')
                    ->modalDescription('Se crearán las obligaciones del mes actual para todos tus clientes activos con régimen fiscal registrado. Las existentes no se duplicarán.')
                    ->action(function () {
                        $generator = app(FiscalObligationGenerator::class);
                        $year = now()->year;
                        $month = now()->month;

                        $clients = Client::query()
                            ->whereNotNull('tax_regime')
                            ->where('status', 'active')
                            ->get();

                        $total = 0;
                        foreach ($clients as $client) {
                            $total += $generator->generateForClient($client, $year, $month)->count();
                        }

                        \Filament\Notifications\Notification::make()
                            ->title("Se generaron {$total} obligaciones nuevas")
                            ->success()
                            ->send();
                    }),

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('due_date', 'asc');
    }
}
