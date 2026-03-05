<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientFileController extends Controller
{
    public function downloadCer(Client $client): StreamedResponse|Response
    {
        Gate::authorize('downloadCertificate', $client);

        if (! $client->efirma_cer_path || ! Storage::disk('local')->exists($client->efirma_cer_path)) {
            abort(404, 'Archivo .cer no encontrado.');
        }

        $filename = $this->buildFilename($client, 'cer');

        return Storage::disk('local')->download($client->efirma_cer_path, $filename);
    }

    public function downloadKey(Client $client): StreamedResponse|Response
    {
        Gate::authorize('downloadCertificate', $client);

        if (! $client->efirma_key_path || ! Storage::disk('local')->exists($client->efirma_key_path)) {
            abort(404, 'Archivo .key no encontrado.');
        }

        $filename = $this->buildFilename($client, 'key');

        return Storage::disk('local')->download($client->efirma_key_path, $filename);
    }

    /**
     * Construye un nombre de archivo descriptivo para la descarga.
     */
    private function buildFilename(Client $client, string $extension): string
    {
        $base = $client->tax_id
            ? strtoupper($client->tax_id)
            : 'cliente_'.$client->id;

        return $base.'.'.$extension;
    }
}
