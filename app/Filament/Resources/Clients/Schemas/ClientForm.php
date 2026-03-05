<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Expediente')
                    ->tabs([
                        Tab::make('Tipo')
                            ->label('Tipo de Contribuyente')
                            ->schema([
                                Select::make('firm_id')
                                    ->label('Despacho')
                                    ->relationship('firm', 'name')
                                    ->required()
                                    ->columnSpanFull(),
                                Select::make('person_type')
                                    ->label('Tipo de persona')
                                    ->options([
                                        'fisica' => 'Persona Física',
                                        'moral'  => 'Persona Moral',
                                    ])
                                    ->required()
                                    ->default('moral')
                                    ->live()
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('Datos Generales')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('name')
                                        ->label('Nombre / Razón social')
                                        ->required(),
                                    TextInput::make('tax_id')
                                        ->label('RFC')
                                        ->required()
                                        ->maxLength(13),
                                    TextInput::make('curp')
                                        ->label('CURP')
                                        ->maxLength(18)
                                        ->visible(fn ($get) => $get('person_type') === 'fisica'),
                                    Select::make('tax_regime')
                                        ->label('Régimen fiscal (SAT)')
                                        ->options([
                                            '601' => '601 – General de Ley Personas Morales',
                                            '603' => '603 – Personas Morales con Fines no Lucrativos',
                                            '605' => '605 – Sueldos y Salarios e Ingresos Asimilados',
                                            '606' => '606 – Arrendamiento',
                                            '612' => '612 – Personas Físicas con Actividades Empresariales',
                                            '621' => '621 – Incorporación Fiscal',
                                            '625' => '625 – Plataformas Tecnológicas',
                                            '626' => '626 – Régimen Simplificado de Confianza (RESICO)',
                                        ])
                                        ->searchable(),
                                    TextInput::make('email')
                                        ->label('Correo electrónico')
                                        ->email(),
                                    TextInput::make('phone')
                                        ->label('Teléfono')
                                        ->tel(),
                                ]),
                            ]),

                        Tab::make('Representante Legal')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('legal_rep_name')
                                        ->label('Nombre del representante legal'),
                                    TextInput::make('legal_rep_rfc')
                                        ->label('RFC del representante')
                                        ->maxLength(13),
                                ]),
                            ]),

                        Tab::make('Actividad Económica')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('economic_activity')
                                        ->label('Giro / Actividad económica'),
                                    TextInput::make('scian_code')
                                        ->label('Código SCIAN')
                                        ->maxLength(10),
                                    TextInput::make('employee_count')
                                        ->label('Número de empleados')
                                        ->numeric()
                                        ->minValue(0),
                                ]),
                            ]),

                        Tab::make('Domicilio Fiscal')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('address')
                                        ->label('Calle y número')
                                        ->columnSpan(2),
                                    TextInput::make('city')
                                        ->label('Ciudad / Municipio'),
                                    TextInput::make('state')
                                        ->label('Estado'),
                                    TextInput::make('postal_code')
                                        ->label('Código postal')
                                        ->maxLength(12),
                                    TextInput::make('country')
                                        ->label('País')
                                        ->default('MX'),
                                ]),
                            ]),

                        Tab::make('Operación y Cumplimiento')
                            ->schema([
                                Grid::make(2)->schema([
                                    Select::make('status')
                                        ->label('Estatus')
                                        ->options([
                                            'active'     => 'Activo',
                                            'inactive'   => 'Inactivo',
                                            'onboarding' => 'En alta',
                                        ])
                                        ->required()
                                        ->default('active'),
                                    Select::make('billing_cycle')
                                        ->label('Ciclo de facturación')
                                        ->options([
                                            'monthly'   => 'Mensual',
                                            'quarterly' => 'Trimestral',
                                            'annual'    => 'Anual',
                                        ])
                                        ->required()
                                        ->default('monthly'),
                                    Select::make('obligations_periodicity')
                                        ->label('Periodicidad de obligaciones')
                                        ->options([
                                            'mensual'   => 'Mensual',
                                            'bimestral' => 'Bimestral',
                                            'anual'     => 'Anual',
                                        ]),
                                    Select::make('compliance_level')
                                        ->label('Nivel de cumplimiento')
                                        ->options([
                                            'high'   => 'Alto',
                                            'medium' => 'Medio',
                                            'low'    => 'Bajo',
                                        ])
                                        ->required()
                                        ->default('medium'),
                                    DatePicker::make('relationship_started_at')
                                        ->label('Inicio de relación'),
                                    DateTimePicker::make('onboarding_completed_at')
                                        ->label('Alta completada el'),
                                    Textarea::make('onboarding_notes')
                                        ->label('Notas de alta')
                                        ->columnSpan(2),
                                ]),
                            ]),

                        Tab::make('Accesos SAT')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('portal_sat_user')
                                        ->label('Usuario portal SAT'),
                                    TextInput::make('portal_sat_password')
                                        ->label('Contraseña portal SAT')
                                        ->password()
                                        ->revealable(),
                                ]),
                            ]),

                        Tab::make('e.firma FIEL')
                            ->schema([
                                Grid::make(2)->schema([
                                    FileUpload::make('efirma_cer_path')
                                        ->label('Certificado .cer')
                                        ->directory('efirma')
                                        ->visibility('private'),
                                    FileUpload::make('efirma_key_path')
                                        ->label('Llave privada .key')
                                        ->directory('efirma')
                                        ->visibility('private'),
                                ]),
                            ]),

                        Tab::make('Documentación')
                            ->schema([
                                Section::make('Documentos recibidos')->schema([
                                    Checkbox::make('documents.constancia_situacion_fiscal')
                                        ->label('Constancia de situación fiscal'),
                                    Checkbox::make('documents.poder_notarial')
                                        ->label('Poder notarial'),
                                    Checkbox::make('documents.identificacion')
                                        ->label('Identificación oficial'),
                                ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
