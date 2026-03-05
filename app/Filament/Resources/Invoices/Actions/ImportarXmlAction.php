<?php

namespace App\Filament\Resources\Invoices\Actions;

use App\Models\Client;
use App\Models\Invoice;
use App\Services\XmlCfdiParser;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImportarXmlAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'importar_xml';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Importar XML')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('primary')
            ->modalHeading('Importar facturas XML (CFDI)')
            ->modalDescription('Selecciona el cliente y el tipo de factura, luego sube uno o varios archivos XML del SAT.')
            ->modalWidth('lg')
            ->form([
                Select::make('client_id')
                    ->label('Cliente')
                    ->options(Client::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('type')
                    ->label('Tipo de factura')
                    ->options([
                        'ingreso' => 'Ingreso (emitida por el cliente)',
                        'gasto' => 'Gasto (recibida por el cliente)',
                    ])
                    ->required()
                    ->default('gasto'),

                FileUpload::make('xml_files')
                    ->label('Archivos XML')
                    ->directory('invoices/xml')
                    ->acceptedFileTypes(['text/xml', 'application/xml'])
                    ->multiple()
                    ->required()
                    ->minFiles(1),
            ])
            ->action(function (array $data): void {
                $parser = new XmlCfdiParser;
                $importadas = 0;
                $errores = [];

                foreach ((array) $data['xml_files'] as $file) {
                    $path = $file instanceof TemporaryUploadedFile
                        ? $file->store('invoices/xml', 'local')
                        : $file;

                    try {
                        $content = Storage::disk('local')->get($path);

                        if (! $content) {
                            $errores[] = basename((string) $path).': no se pudo leer el archivo.';

                            continue;
                        }

                        $parsed = $parser->parse($content);

                        Invoice::create([
                            'client_id' => $data['client_id'],
                            'type' => $data['type'],
                            'xml_path' => $path,
                            'uuid' => $parsed['uuid'],
                            'serie' => $parsed['serie'],
                            'folio' => $parsed['folio'],
                            'fecha_emision' => $parsed['fecha_emision'],
                            'rfc_emisor' => $parsed['rfc_emisor'],
                            'nombre_emisor' => $parsed['nombre_emisor'],
                            'rfc_receptor' => $parsed['rfc_receptor'],
                            'nombre_receptor' => $parsed['nombre_receptor'],
                            'uso_cfdi' => $parsed['uso_cfdi'],
                            'tipo_comprobante' => $parsed['tipo_comprobante'],
                            'metodo_pago' => $parsed['metodo_pago'],
                            'forma_pago' => $parsed['forma_pago'],
                            'moneda' => $parsed['moneda'],
                            'subtotal' => $parsed['subtotal'],
                            'descuento' => $parsed['descuento'],
                            'iva' => $parsed['iva'],
                            'isr_retenido' => $parsed['isr_retenido'],
                            'iva_retenido' => $parsed['iva_retenido'],
                            'total' => $parsed['total'],
                            'concepto_principal' => $parsed['concepto_principal'],
                            'status' => 'procesado',
                        ]);

                        $importadas++;
                    } catch (\RuntimeException $e) {
                        $nombre = basename((string) $path);

                        Invoice::create([
                            'client_id' => $data['client_id'],
                            'type' => $data['type'],
                            'xml_path' => $path,
                            'status' => 'error',
                            'parse_error' => $e->getMessage(),
                        ]);

                        $errores[] = $nombre.': '.$e->getMessage();
                    }
                }

                if ($importadas > 0 && count($errores) === 0) {
                    Notification::make()
                        ->title(($importadas === 1 ? '1 factura importada' : "{$importadas} facturas importadas").' correctamente')
                        ->success()
                        ->send();

                    return;
                }

                if ($importadas > 0 && count($errores) > 0) {
                    Notification::make()
                        ->title("{$importadas} importadas, ".count($errores).' con error')
                        ->body(implode("\n", $errores))
                        ->warning()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title('No se pudo importar ninguna factura')
                    ->body(implode("\n", $errores))
                    ->danger()
                    ->send();
            });
    }
}
