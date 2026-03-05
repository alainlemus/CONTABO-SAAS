<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\FiscalObligation;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FiscalObligationFileController extends Controller
{
    public function downloadAcuse(FiscalObligation $fiscalObligation): StreamedResponse|Response
    {
        $this->authorizeAccess($fiscalObligation);

        if (! $fiscalObligation->acuse_pdf_path || ! Storage::disk('local')->exists($fiscalObligation->acuse_pdf_path)) {
            abort(404, 'Archivo PDF no encontrado.');
        }

        $filename = $this->buildFilename($fiscalObligation);

        return Storage::disk('local')->download($fiscalObligation->acuse_pdf_path, $filename);
    }

    /**
     * Verifica que el usuario autenticado sea el dueño del cliente asociado a la obligación.
     */
    private function authorizeAccess(FiscalObligation $fiscalObligation): void
    {
        $ownerId = auth()->user()->ownerId();

        $clientUserId = Client::withoutGlobalScopes()
            ->where('id', $fiscalObligation->client_id)
            ->value('user_id');

        if ($clientUserId !== $ownerId) {
            abort(403);
        }
    }

    /**
     * Construye un nombre de archivo descriptivo para la descarga.
     */
    private function buildFilename(FiscalObligation $fiscalObligation): string
    {
        $parts = array_filter([
            $fiscalObligation->client?->tax_id
                ? strtoupper($fiscalObligation->client->tax_id)
                : null,
            $fiscalObligation->type->value,
            $fiscalObligation->periodLabel(),
        ]);

        $base = $parts ? implode('_', $parts) : 'acuse_'.$fiscalObligation->id;

        return str($base)->slug('_')->toString().'.pdf';
    }
}
