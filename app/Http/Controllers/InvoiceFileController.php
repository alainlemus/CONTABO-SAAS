<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceFileController extends Controller
{
    public function downloadXml(Invoice $invoice): StreamedResponse|Response
    {
        $this->authorizeInvoiceAccess($invoice);

        if (! $invoice->xml_path || ! Storage::disk('local')->exists($invoice->xml_path)) {
            abort(404, 'Archivo XML no encontrado.');
        }

        $filename = $this->buildFilename($invoice, 'xml');

        return Storage::disk('local')->download($invoice->xml_path, $filename);
    }

    public function downloadPdf(Invoice $invoice): StreamedResponse|Response
    {
        $this->authorizeInvoiceAccess($invoice);

        if (! $invoice->pdf_path || ! Storage::disk('local')->exists($invoice->pdf_path)) {
            abort(404, 'Archivo PDF no encontrado.');
        }

        $filename = $this->buildFilename($invoice, 'pdf');

        return Storage::disk('local')->download($invoice->pdf_path, $filename);
    }

    /**
     * Verifica que el usuario autenticado sea el dueño del cliente asociado a la factura.
     */
    private function authorizeInvoiceAccess(Invoice $invoice): void
    {
        $ownerId = auth()->user()->ownerId();

        $clientUserId = Client::withoutGlobalScopes()
            ->where('id', $invoice->client_id)
            ->value('user_id');

        if ($clientUserId !== $ownerId) {
            abort(403);
        }
    }

    /**
     * Construye un nombre de archivo descriptivo para la descarga.
     */
    private function buildFilename(Invoice $invoice, string $extension): string
    {
        $parts = array_filter([
            $invoice->serie,
            $invoice->folio,
            $invoice->uuid ? substr($invoice->uuid, 0, 8) : null,
        ]);

        $base = $parts ? implode('_', $parts) : 'factura_'.$invoice->id;

        return $base.'.'.$extension;
    }
}
