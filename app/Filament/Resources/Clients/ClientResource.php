<?php

namespace App\Filament\Resources\Clients;

use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Filament\Resources\Clients\Pages\ViewClient;
use App\Filament\Resources\Clients\RelationManagers\FiscalObligationsRelationManager;
use App\Filament\Resources\Clients\RelationManagers\InvoicesRelationManager;
use App\Filament\Resources\Clients\RelationManagers\NotesRelationManager;
use App\Filament\Resources\Clients\Schemas\ClientForm;
use App\Filament\Resources\Clients\Tables\ClientsTable;
use App\Models\Client;
use BackedEnum;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $pluralModelLabel = 'Clientes';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\UnitEnum|null $navigationGroup = 'Cartera';

    protected static ?int $navigationSort = 1;

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'tax_id', 'email'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'RFC' => $record->tax_id,
            'Régimen' => $record->tax_regime ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return ClientForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos generales')
                ->icon(Heroicon::OutlinedIdentification)
                ->columns(3)
                ->schema([
                    TextEntry::make('name')
                        ->label('Nombre / Razón social')
                        ->weight('semibold'),
                    TextEntry::make('tax_id')
                        ->label('RFC')
                        ->copyable(),
                    TextEntry::make('curp')
                        ->label('CURP')
                        ->placeholder('—'),
                    TextEntry::make('person_type')
                        ->label('Tipo de persona')
                        ->badge()
                        ->color(fn (?string $state): string => match ($state) {
                            'fisica' => 'info',
                            'moral' => 'primary',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn (?string $state): string => match ($state) {
                            'fisica' => 'Persona Física',
                            'moral' => 'Persona Moral',
                            default => $state ?? '—',
                        }),
                    TextEntry::make('tax_regime')
                        ->label('Régimen fiscal')
                        ->formatStateUsing(fn (?string $state): string => match ($state) {
                            '601' => '601 – General de Ley PM',
                            '603' => '603 – PM sin Fines Lucrativos',
                            '605' => '605 – Sueldos y Salarios',
                            '606' => '606 – Arrendamiento',
                            '612' => '612 – Actividades Empresariales',
                            '621' => '621 – Incorporación Fiscal',
                            '625' => '625 – Plataformas Tecnológicas',
                            '626' => '626 – RESICO',
                            default => $state ?? '—',
                        })
                        ->placeholder('—'),
                    TextEntry::make('status')
                        ->label('Estatus')
                        ->badge()
                        ->color(fn (?string $state): string => match ($state) {
                            'active' => 'success',
                            'inactive' => 'danger',
                            'onboarding' => 'warning',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn (?string $state): string => match ($state) {
                            'active' => 'Activo',
                            'inactive' => 'Inactivo',
                            'onboarding' => 'En alta',
                            default => $state ?? '—',
                        }),
                    TextEntry::make('compliance_level')
                        ->label('Cumplimiento')
                        ->badge()
                        ->color(fn (?string $state): string => match ($state) {
                            'high' => 'success',
                            'medium' => 'warning',
                            'low' => 'danger',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn (?string $state): string => match ($state) {
                            'high' => 'Alto',
                            'medium' => 'Medio',
                            'low' => 'Bajo',
                            default => $state ?? '—',
                        }),
                    TextEntry::make('economic_activity')
                        ->label('Giro / Actividad')
                        ->placeholder('—'),
                    TextEntry::make('employee_count')
                        ->label('Empleados')
                        ->placeholder('—'),
                ]),

            Grid::make(2)->schema([
                Section::make('Contacto')
                    ->icon(Heroicon::OutlinedPhone)
                    ->schema([
                        TextEntry::make('email')
                            ->label('Correo electrónico')
                            ->placeholder('—')
                            ->copyable(),
                        TextEntry::make('phone')
                            ->label('Teléfono')
                            ->placeholder('—'),
                        TextEntry::make('address')
                            ->label('Dirección')
                            ->placeholder('—'),
                        TextEntry::make('city')
                            ->label('Ciudad')
                            ->placeholder('—'),
                        TextEntry::make('state')
                            ->label('Estado')
                            ->placeholder('—'),
                        TextEntry::make('postal_code')
                            ->label('C.P.')
                            ->placeholder('—'),
                    ]),

                Section::make('Configuración fiscal')
                    ->icon(Heroicon::OutlinedClipboardDocumentCheck)
                    ->schema([
                        TextEntry::make('obligations_periodicity')
                            ->label('Periodicidad obligaciones')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'mensual' => 'Mensual',
                                'bimestral' => 'Bimestral',
                                'anual' => 'Anual',
                                default => $state ?? '—',
                            })
                            ->placeholder('—'),
                        TextEntry::make('billing_cycle')
                            ->label('Ciclo de facturación')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'monthly' => 'Mensual',
                                'quarterly' => 'Trimestral',
                                'annual' => 'Anual',
                                default => $state ?? '—',
                            }),
                        TextEntry::make('relationship_started_at')
                            ->label('Inicio de relación')
                            ->date('d/m/Y')
                            ->placeholder('—'),
                        TextEntry::make('portal_sat_user')
                            ->label('Usuario SAT')
                            ->placeholder('—')
                            ->copyable(),
                        IconEntry::make('efirma_cer_path')
                            ->label('e.firma .CER')
                            ->boolean()
                            ->trueIcon('heroicon-o-shield-check')
                            ->falseIcon('heroicon-o-minus')
                            ->trueColor('success')
                            ->falseColor('gray')
                            ->state(fn (Client $record): bool => (bool) $record->efirma_cer_path),
                        IconEntry::make('efirma_key_path')
                            ->label('e.firma .KEY')
                            ->boolean()
                            ->trueIcon('heroicon-o-key')
                            ->falseIcon('heroicon-o-minus')
                            ->trueColor('warning')
                            ->falseColor('gray')
                            ->state(fn (Client $record): bool => (bool) $record->efirma_key_path),
                    ]),
            ]),

            Section::make('Representante legal')
                ->icon(Heroicon::OutlinedUserCircle)
                ->columns(2)
                ->collapsed()
                ->schema([
                    TextEntry::make('legal_rep_name')
                        ->label('Nombre')
                        ->placeholder('—'),
                    TextEntry::make('legal_rep_rfc')
                        ->label('RFC')
                        ->placeholder('—'),
                ]),

            Section::make('Onboarding')
                ->icon(Heroicon::OutlinedRocketLaunch)
                ->columns(2)
                ->collapsed()
                ->schema([
                    TextEntry::make('onboarding_completed_at')
                        ->label('Alta completada el')
                        ->dateTime('d/m/Y H:i')
                        ->placeholder('Pendiente'),
                    TextEntry::make('onboarding_notes')
                        ->label('Notas de alta')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return ClientsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            FiscalObligationsRelationManager::class,
            InvoicesRelationManager::class,
            NotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClients::route('/'),
            'create' => CreateClient::route('/create'),
            'view' => ViewClient::route('/{record}'),
            'edit' => EditClient::route('/{record}/edit'),
        ];
    }
}
