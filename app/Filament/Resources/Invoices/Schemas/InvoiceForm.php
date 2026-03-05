<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Services\XmlCfdiParser;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Identificación')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->description('Cliente, tipo y archivo XML de la factura')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('client_id')
                            ->label('Cliente')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        Select::make('type')
                            ->label('Tipo de factura')
                            ->options([
                                'ingreso' => 'Ingreso (factura emitida por el cliente)',
                                'gasto' => 'Gasto (factura recibida por el cliente)',
                            ])
                            ->required()
                            ->default('gasto'),

                        Select::make('status')
                            ->label('Estatus')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'procesado' => 'Procesado',
                                'error' => 'Error',
                            ])
                            ->default('pendiente')
                            ->required(),
                    ]),

                Section::make('Archivos')
                    ->icon(Heroicon::OutlinedPaperClip)
                    ->description('XML obligatorio — PDF opcional como respaldo visual')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('xml_path')
                            ->label('XML del SAT (CFDI)')
                            ->directory('invoices/xml')
                            ->acceptedFileTypes(['text/xml', 'application/xml'])
                            ->live()
                            ->afterStateUpdated(function (?string $state, Set $set) {
                                if (! $state) {
                                    return;
                                }

                                try {
                                    $content = Storage::disk('local')->get($state);
                                    if (! $content) {
                                        return;
                                    }

                                    $data = (new XmlCfdiParser)->parse($content);

                                    $set('uuid', $data['uuid']);
                                    $set('serie', $data['serie']);
                                    $set('folio', $data['folio']);
                                    $set('fecha_emision', $data['fecha_emision']);
                                    $set('rfc_emisor', $data['rfc_emisor']);
                                    $set('nombre_emisor', $data['nombre_emisor']);
                                    $set('rfc_receptor', $data['rfc_receptor']);
                                    $set('nombre_receptor', $data['nombre_receptor']);
                                    $set('uso_cfdi', $data['uso_cfdi']);
                                    $set('tipo_comprobante', $data['tipo_comprobante']);
                                    $set('metodo_pago', $data['metodo_pago']);
                                    $set('forma_pago', $data['forma_pago']);
                                    $set('moneda', $data['moneda']);
                                    $set('subtotal', $data['subtotal']);
                                    $set('descuento', $data['descuento']);
                                    $set('iva', $data['iva']);
                                    $set('isr_retenido', $data['isr_retenido']);
                                    $set('iva_retenido', $data['iva_retenido']);
                                    $set('total', $data['total']);
                                    $set('concepto_principal', $data['concepto_principal']);
                                    $set('status', 'procesado');
                                    $set('parse_error', null);
                                } catch (\RuntimeException $e) {
                                    $set('status', 'error');
                                    $set('parse_error', $e->getMessage());
                                }
                            }),

                        FileUpload::make('pdf_path')
                            ->label('PDF (opcional)')
                            ->directory('invoices/pdf')
                            ->acceptedFileTypes(['application/pdf'])
                            ->nullable(),
                    ]),

                Section::make('Datos del CFDI')
                    ->icon(Heroicon::OutlinedInformationCircle)
                    ->description('Extraídos automáticamente del XML — puedes corregirlos si es necesario')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('uuid')
                            ->label('UUID / Folio fiscal')
                            ->maxLength(36)
                            ->columnSpanFull(),

                        TextInput::make('fecha_emision')
                            ->label('Fecha de emisión')
                            ->placeholder('YYYY-MM-DD'),

                        TextInput::make('tipo_comprobante')
                            ->label('Tipo de comprobante')
                            ->maxLength(1),

                        TextInput::make('serie')
                            ->label('Serie'),

                        TextInput::make('folio')
                            ->label('Folio'),

                        TextInput::make('metodo_pago')
                            ->label('Método de pago'),

                        TextInput::make('forma_pago')
                            ->label('Forma de pago'),

                        TextInput::make('moneda')
                            ->label('Moneda')
                            ->default('MXN'),

                        TextInput::make('uso_cfdi')
                            ->label('Uso del CFDI'),
                    ]),

                Section::make('Emisor y Receptor')
                    ->icon(Heroicon::OutlinedBuildingOffice2)
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('rfc_emisor')
                            ->label('RFC Emisor')
                            ->maxLength(13),

                        TextInput::make('nombre_emisor')
                            ->label('Nombre Emisor'),

                        TextInput::make('rfc_receptor')
                            ->label('RFC Receptor')
                            ->maxLength(13),

                        TextInput::make('nombre_receptor')
                            ->label('Nombre Receptor'),

                        TextInput::make('concepto_principal')
                            ->label('Concepto principal')
                            ->columnSpanFull(),
                    ]),

                Section::make('Importes')
                    ->icon(Heroicon::OutlinedCurrencyDollar)
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('$'),

                        TextInput::make('descuento')
                            ->label('Descuento')
                            ->numeric()
                            ->prefix('$'),

                        TextInput::make('iva')
                            ->label('IVA trasladado')
                            ->numeric()
                            ->prefix('$'),

                        TextInput::make('total')
                            ->label('Total')
                            ->numeric()
                            ->prefix('$'),

                        TextInput::make('isr_retenido')
                            ->label('ISR retenido')
                            ->numeric()
                            ->prefix('$'),

                        TextInput::make('iva_retenido')
                            ->label('IVA retenido')
                            ->numeric()
                            ->prefix('$'),
                    ]),
            ]);
    }
}
