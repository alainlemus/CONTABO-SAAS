<?php

namespace App\Observers;

use App\Models\Client;
use App\Services\FiscalObligationGenerator;

class ClientObserver
{
    public function __construct(
        protected FiscalObligationGenerator $generator,
    ) {}

    /**
     * Cuando se crea un cliente con régimen fiscal, genera las obligaciones del año en curso.
     */
    public function created(Client $client): void
    {
        if (! $client->tax_regime) {
            return;
        }

        $this->generator->generateYearForClient($client, now()->year);
    }

    /**
     * Cuando se actualiza el régimen fiscal de un cliente, genera las obligaciones
     * del año en curso para el nuevo régimen (sin duplicar las ya existentes).
     */
    public function updated(Client $client): void
    {
        if (! $client->wasChanged('tax_regime') || ! $client->tax_regime) {
            return;
        }

        $this->generator->generateYearForClient($client, now()->year);
    }

    public function deleted(Client $client): void {}

    public function restored(Client $client): void {}

    public function forceDeleted(Client $client): void {}
}
