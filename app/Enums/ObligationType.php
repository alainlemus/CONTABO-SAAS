<?php

namespace App\Enums;

enum ObligationType: string
{
    // ── Mensuales ────────────────────────────────────────────────────────────
    case IsrMensual = 'isr_mensual';
    case IvaMensual = 'iva_mensual';
    case Diot = 'diot';             // Declaración Informativa de Operaciones con Terceros

    // ── Bimestrales ──────────────────────────────────────────────────────────
    case IsrBimestral = 'isr_bimestral';   // RIF / RESICO persona física
    case IvaBimestral = 'iva_bimestral';   // RIF / RESICO persona física
    case ImssBimestral = 'imss_bimestral'; // Cuotas IMSS bimestral (empleadores)

    // ── Anuales ──────────────────────────────────────────────────────────────
    case IsrAnual = 'isr_anual';
    case DeclaracionAnualPf = 'declaracion_anual_pf';   // Declaración anual PF
    case DeclaracionAnualPm = 'declaracion_anual_pm';   // Declaración anual PM

    public function label(): string
    {
        return match ($this) {
            self::IsrMensual => 'ISR Mensual',
            self::IvaMensual => 'IVA Mensual',
            self::Diot => 'DIOT',
            self::IsrBimestral => 'ISR Bimestral',
            self::IvaBimestral => 'IVA Bimestral',
            self::ImssBimestral => 'IMSS Bimestral',
            self::IsrAnual => 'ISR Anual',
            self::DeclaracionAnualPf => 'Declaración Anual PF',
            self::DeclaracionAnualPm => 'Declaración Anual PM',
        };
    }

    public function periodicity(): string
    {
        return match ($this) {
            self::IsrMensual, self::IvaMensual, self::Diot => 'mensual',
            self::IsrBimestral, self::IvaBimestral, self::ImssBimestral => 'bimestral',
            self::IsrAnual, self::DeclaracionAnualPf, self::DeclaracionAnualPm => 'anual',
        };
    }

    /** Día límite del mes siguiente al período (o del mismo mes para anuales). */
    public function dueDayOfNextMonth(): int
    {
        return match ($this) {
            self::IsrMensual => 17,
            self::IvaMensual => 17,
            self::Diot => 17,
            self::IsrBimestral => 17,
            self::IvaBimestral => 17,
            self::ImssBimestral => 17,
            self::IsrAnual => 31,           // Personas morales: 31 de marzo
            self::DeclaracionAnualPf => 30, // Personas físicas: 30 de abril
            self::DeclaracionAnualPm => 31,
        };
    }

    /**
     * Retorna las obligaciones aplicables para un régimen fiscal SAT dado.
     *
     * @return array<self>
     */
    public static function forRegime(string $taxRegime): array
    {
        return match ($taxRegime) {
            // 601 — General de Ley Personas Morales
            '601' => [self::IsrMensual, self::IvaMensual, self::Diot, self::DeclaracionAnualPm],

            // 603 — Personas Morales con Fines no Lucrativos
            '603' => [self::IsrMensual, self::DeclaracionAnualPm],

            // 605 — Sueldos y Salarios e Ingresos Asimilados a Salarios
            '605' => [self::DeclaracionAnualPf],

            // 606 — Arrendamiento
            '606' => [self::IsrMensual, self::IvaMensual, self::DeclaracionAnualPf],

            // 612 — Personas Físicas con Actividades Empresariales y Profesionales
            '612' => [self::IsrMensual, self::IvaMensual, self::Diot, self::DeclaracionAnualPf],

            // 621 — Incorporación Fiscal (RIF) — declaraciones bimestrales
            '621' => [self::IsrBimestral, self::IvaBimestral, self::DeclaracionAnualPf],

            // 625 — Régimen de Actividades Empresariales con ingresos por Plataformas Tecnológicas
            '625' => [self::IsrMensual, self::IvaMensual, self::DeclaracionAnualPf],

            // 626 — Régimen Simplificado de Confianza (RESICO)
            '626' => [self::IsrMensual, self::IvaMensual, self::DeclaracionAnualPf],

            default => [],
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
