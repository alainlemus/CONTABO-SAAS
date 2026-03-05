<?php

namespace App\Models;

use App\Enums\ObligationStatus;
use App\Enums\ObligationType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FiscalObligation extends Model
{
    /** @use HasFactory<\Database\Factories\FiscalObligationFactory> */
    use HasFactory;

    protected $fillable = [
        'client_id',
        'type',
        'period_year',
        'period_month',
        'due_date',
        'status',
        'presented_at',
        'reference',
        'notes',
        'acuse_pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'type' => ObligationType::class,
            'status' => ObligationStatus::class,
            'period_year' => 'integer',
            'period_month' => 'integer',
            'due_date' => 'date',
            'presented_at' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('owned', function (Builder $query): void {
            if (! auth()->check()) {
                return;
            }

            $query->whereHas('client', function (Builder $q): void {
                $q->where('user_id', auth()->user()->ownerId());
            });
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class)->withoutGlobalScopes();
    }

    public function isOverdue(): bool
    {
        return $this->status === ObligationStatus::Pending
            && $this->due_date->isPast();
    }

    public function acusePdfExists(): bool
    {
        return (bool) $this->acuse_pdf_path;
    }

    /** Nombre legible del período, ej. "Enero 2025" o "2025". */
    public function periodLabel(): string
    {
        if ($this->period_month === null) {
            return (string) $this->period_year;
        }

        $months = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        return ($months[$this->period_month] ?? $this->period_month).' '.$this->period_year;
    }
}
