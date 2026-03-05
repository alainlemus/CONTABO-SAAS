<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Datos Generales')
                    ->icon(Heroicon::OutlinedIdentification)
                    ->description('Tipo de contribuyente e información básica')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('person_type')
                            ->label('Tipo de persona')
                            ->options([
                                'fisica' => 'Persona Física',
                                'moral' => 'Persona Moral',
                            ])
                            ->required()
                            ->default('moral')
                            ->live()
                            ->columnSpanFull(),
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

                Section::make('Representante Legal')
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->description('Datos del representante legal (personas morales)')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('legal_rep_name')
                            ->label('Nombre del representante legal'),
                        TextInput::make('legal_rep_rfc')
                            ->label('RFC del representante')
                            ->maxLength(13),
                    ]),

                Section::make('Actividad Económica')
                    ->icon(Heroicon::OutlinedBriefcase)
                    ->description('Giro, código SCIAN y número de empleados')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
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

                Section::make('Domicilio Fiscal')
                    ->icon(Heroicon::OutlinedMapPin)
                    ->description('Dirección registrada ante el SAT')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
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

                Section::make('Operación y Cumplimiento')
                    ->icon(Heroicon::OutlinedClipboardDocumentCheck)
                    ->description('Estatus, facturación y obligaciones fiscales')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('status')
                            ->label('Estatus')
                            ->options([
                                'active' => 'Activo',
                                'inactive' => 'Inactivo',
                                'onboarding' => 'En alta',
                            ])
                            ->required()
                            ->default('active'),
                        Select::make('billing_cycle')
                            ->label('Ciclo de facturación')
                            ->options([
                                'monthly' => 'Mensual',
                                'quarterly' => 'Trimestral',
                                'annual' => 'Anual',
                            ])
                            ->required()
                            ->default('monthly'),
                        Select::make('obligations_periodicity')
                            ->label('Periodicidad de obligaciones')
                            ->options([
                                'mensual' => 'Mensual',
                                'bimestral' => 'Bimestral',
                                'anual' => 'Anual',
                            ]),
                        Select::make('compliance_level')
                            ->label('Nivel de cumplimiento')
                            ->options([
                                'high' => 'Alto',
                                'medium' => 'Medio',
                                'low' => 'Bajo',
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

                Section::make('Accesos SAT')
                    ->icon(Heroicon::OutlinedKey)
                    ->description('Credenciales del portal del SAT')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('portal_sat_user')
                            ->label('Usuario portal SAT'),
                        TextInput::make('portal_sat_password')
                            ->label('Contraseña portal SAT')
                            ->password()
                            ->revealable(),
                    ]),

                Section::make('e.firma FIEL')
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->description('Certificado y llave privada de la e.firma')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('efirma_cer_path')
                            ->label('Certificado .cer')
                            ->directory('efirma')
                            ->visibility('private'),
                        FileUpload::make('efirma_key_path')
                            ->label('Llave privada .key')
                            ->directory('efirma')
                            ->visibility('private'),
                    ]),

                Section::make('Documentación')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->description('Documentos recibidos del cliente')
                    ->columnSpanFull()
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
            ]);
    }

    /** @return array<Step> */
    public static function wizardSteps(): array
    {
        return [
            static::datosGeneralesStep(),
            static::representanteLegalStep(),
            static::actividadEconomicaStep(),
            static::domicilioFiscalStep(),
            static::operacionCumplimientoStep(),
            static::accesosSatStep(),
            static::efirmaStep(),
            static::documentacionStep(),
        ];
    }

    public static function datosGeneralesStep(): Step
    {
        return Step::make('Datos Generales')
            ->icon(Heroicon::OutlinedIdentification)
            ->description('Tipo de contribuyente e información básica')
            ->columns(2)
            ->schema([
                Select::make('person_type')
                    ->label('Tipo de persona')
                    ->options([
                        'fisica' => 'Persona Física',
                        'moral' => 'Persona Moral',
                    ])
                    ->required()
                    ->default('moral')
                    ->live()
                    ->columnSpanFull(),
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
            ]);
    }

    public static function representanteLegalStep(): Step
    {
        return Step::make('Representante Legal')
            ->icon(Heroicon::OutlinedUserCircle)
            ->description('Datos del representante legal (personas morales)')
            ->columns(2)
            ->schema([
                TextInput::make('legal_rep_name')
                    ->label('Nombre del representante legal'),
                TextInput::make('legal_rep_rfc')
                    ->label('RFC del representante')
                    ->maxLength(13),
            ]);
    }

    public static function actividadEconomicaStep(): Step
    {
        return Step::make('Actividad Económica')
            ->icon(Heroicon::OutlinedBriefcase)
            ->description('Giro, código SCIAN y número de empleados')
            ->columns(2)
            ->schema([
                TextInput::make('economic_activity')
                    ->label('Giro / Actividad económica'),
                TextInput::make('scian_code')
                    ->label('Código SCIAN')
                    ->maxLength(10),
                TextInput::make('employee_count')
                    ->label('Número de empleados')
                    ->numeric()
                    ->minValue(0),
            ]);
    }

    public static function domicilioFiscalStep(): Step
    {
        return Step::make('Domicilio Fiscal')
            ->icon(Heroicon::OutlinedMapPin)
            ->description('Dirección registrada ante el SAT')
            ->columns(2)
            ->schema([
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
            ]);
    }

    public static function operacionCumplimientoStep(): Step
    {
        return Step::make('Operación y Cumplimiento')
            ->icon(Heroicon::OutlinedClipboardDocumentCheck)
            ->description('Estatus, facturación y obligaciones fiscales')
            ->columns(2)
            ->schema([
                Select::make('status')
                    ->label('Estatus')
                    ->options([
                        'active' => 'Activo',
                        'inactive' => 'Inactivo',
                        'onboarding' => 'En alta',
                    ])
                    ->required()
                    ->default('active'),
                Select::make('billing_cycle')
                    ->label('Ciclo de facturación')
                    ->options([
                        'monthly' => 'Mensual',
                        'quarterly' => 'Trimestral',
                        'annual' => 'Anual',
                    ])
                    ->required()
                    ->default('monthly'),
                Select::make('obligations_periodicity')
                    ->label('Periodicidad de obligaciones')
                    ->options([
                        'mensual' => 'Mensual',
                        'bimestral' => 'Bimestral',
                        'anual' => 'Anual',
                    ]),
                Select::make('compliance_level')
                    ->label('Nivel de cumplimiento')
                    ->options([
                        'high' => 'Alto',
                        'medium' => 'Medio',
                        'low' => 'Bajo',
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
            ]);
    }

    public static function accesosSatStep(): Step
    {
        return Step::make('Accesos SAT')
            ->icon(Heroicon::OutlinedKey)
            ->description('Credenciales del portal del SAT')
            ->columns(2)
            ->schema([
                TextInput::make('portal_sat_user')
                    ->label('Usuario portal SAT'),
                TextInput::make('portal_sat_password')
                    ->label('Contraseña portal SAT')
                    ->password()
                    ->revealable(),
            ]);
    }

    public static function efirmaStep(): Step
    {
        return Step::make('e.firma FIEL')
            ->icon(Heroicon::OutlinedShieldCheck)
            ->description('Certificado y llave privada de la e.firma')
            ->columns(2)
            ->schema([
                FileUpload::make('efirma_cer_path')
                    ->label('Certificado .cer')
                    ->directory('efirma')
                    ->visibility('private'),
                FileUpload::make('efirma_key_path')
                    ->label('Llave privada .key')
                    ->directory('efirma')
                    ->visibility('private'),
            ]);
    }

    public static function documentacionStep(): Step
    {
        return Step::make('Documentación')
            ->icon(Heroicon::OutlinedDocumentText)
            ->description('Documentos recibidos del cliente')
            ->schema([
                Section::make('Documentos recibidos')->schema([
                    Checkbox::make('documents.constancia_situacion_fiscal')
                        ->label('Constancia de situación fiscal'),
                    Checkbox::make('documents.poder_notarial')
                        ->label('Poder notarial'),
                    Checkbox::make('documents.identificacion')
                        ->label('Identificación oficial'),
                ]),
            ]);
    }
}
