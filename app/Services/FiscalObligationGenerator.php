<?php

namespace App\Services;

use App\Enums\ObligationStatus;
use App\Enums\ObligationType;
use App\Models\Client;
use App\Models\FiscalObligation;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FiscalObligationGenerator
{
    /**
     * Genera (o actualiza) todas las obligaciones fiscales para un cliente
     * en un año y mes determinados. Si ya existen, se omiten (no se duplican).
     *
     * @return Collection<int, FiscalObligation>
     */
    public function generateForClient(Client $client, int $year, int $month): Collection
    {
        if (! $client->tax_regime) {
            return collect();
        }

        $obligationTypes = ObligationType::forRegime($client->tax_regime);
        $generated = collect();

        foreach ($obligationTypes as $type) {
            match ($type->periodicity()) {
                'mensual' => $this->generateMonthly($client, $type, $year, $month, $generated),
                'bimestral' => $this->generateBimonthly($client, $type, $year, $month, $generated),
                'anual' => $this->generateAnnual($client, $type, $year, $generated),
            };
        }

        return $generated;
    }

    /**
     * Genera obligaciones mensuales para el mes/año dado.
     *
     * @param  Collection<int, FiscalObligation>  $generated
     */
    private function generateMonthly(Client $client, ObligationType $type, int $year, int $month, Collection $generated): void
    {
        // Vence el día 17 del mes SIGUIENTE al período
        $dueDate = Carbon::create($year, $month, 1)->addMonthNoOverflow()->setDay($type->dueDayOfNextMonth());

        $obligation = $this->upsert($client, $type, $year, $month, $dueDate);

        if ($obligation) {
            $generated->push($obligation);
        }
    }

    /**
     * Genera obligaciones bimestrales cuando el mes corresponde al cierre de un bimestre.
     * Bimestres: Ene-Feb (vence 17 mar), Mar-Abr (17 may), May-Jun (17 jul),
     *            Jul-Ago (17 sep), Sep-Oct (17 nov), Nov-Dic (17 ene siguiente año).
     *
     * @param  Collection<int, FiscalObligation>  $generated
     */
    private function generateBimonthly(Client $client, ObligationType $type, int $year, int $month, Collection $generated): void
    {
        // Solo genera al inicio del bimestre (meses impares: 1,3,5,7,9,11)
        // pero guardamos con el mes de INICIO del bimestre como referencia
        $bimonthlyStarts = [1, 3, 5, 7, 9, 11];

        if (! in_array($month, $bimonthlyStarts, true)) {
            return;
        }

        // Vence el 17 del mes siguiente al segundo mes del bimestre
        $secondMonth = $month + 1;
        $dueDate = Carbon::create($year, $secondMonth, 1)->addMonthNoOverflow()->setDay($type->dueDayOfNextMonth());

        $obligation = $this->upsert($client, $type, $year, $month, $dueDate);

        if ($obligation) {
            $generated->push($obligation);
        }
    }

    /**
     * Genera la obligación anual (siempre se asocia al enero del año dado).
     * Solo genera si el mes es enero, para evitar duplicados en llamadas mensuales.
     *
     * @param  Collection<int, FiscalObligation>  $generated
     */
    private function generateAnnual(Client $client, ObligationType $type, int $year, Collection $generated): void
    {
        // Anual PF: vence 30 de abril del año siguiente
        // Anual PM: vence 31 de marzo del año siguiente
        $dueMonth = $type === ObligationType::DeclaracionAnualPf ? 4 : 3;
        $dueDate = Carbon::create($year + 1, $dueMonth, $type->dueDayOfNextMonth());

        $obligation = $this->upsert($client, $type, $year, null, $dueDate);

        if ($obligation) {
            $generated->push($obligation);
        }
    }

    /**
     * Genera todas las obligaciones del año completo para un cliente.
     *
     * @return Collection<int, FiscalObligation>
     */
    public function generateYearForClient(Client $client, int $year): Collection
    {
        $generated = collect();

        for ($month = 1; $month <= 12; $month++) {
            $generated = $generated->merge(
                $this->generateForClient($client, $year, $month)
            );
        }

        // Agregar obligaciones anuales (se generan una sola vez)
        if ($client->tax_regime) {
            $annualTypes = collect(ObligationType::forRegime($client->tax_regime))
                ->filter(fn (ObligationType $t) => $t->periodicity() === 'anual');

            foreach ($annualTypes as $type) {
                $this->generateAnnual($client, $type, $year, $generated);
            }
        }

        return $generated->unique('id');
    }

    /**
     * Inserta o recupera una obligación sin modificar las ya existentes.
     * Retorna null si ya existía (no se genera duplicado).
     */
    private function upsert(Client $client, ObligationType $type, int $year, ?int $month, Carbon $dueDate): ?FiscalObligation
    {
        $existing = FiscalObligation::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->where('type', $type->value)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->first();

        if ($existing) {
            return null;
        }

        return FiscalObligation::create([
            'client_id' => $client->id,
            'type' => $type,
            'period_year' => $year,
            'period_month' => $month,
            'due_date' => $dueDate,
            'status' => ObligationStatus::Pending,
        ]);
    }

    /**
     * Marca las obligaciones pendientes cuya fecha de vencimiento ya pasó como 'overdue'.
     * Se puede llamar desde un comando de consola o job programado.
     */
    public function markOverdue(): int
    {
        return FiscalObligation::withoutGlobalScopes()
            ->where('status', ObligationStatus::Pending)
            ->where('due_date', '<', Carbon::today())
            ->update(['status' => ObligationStatus::Overdue]);
    }
}
