<?php

namespace App\Enums;

enum ObligationStatus: string
{
    case Pending = 'pending';
    case Presented = 'presented';
    case NotApplicable = 'not_applicable';
    case Overdue = 'overdue';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Presented => 'Presentada',
            self::NotApplicable => 'No aplica',
            self::Overdue => 'Vencida',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Presented => 'success',
            self::NotApplicable => 'gray',
            self::Overdue => 'danger',
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
