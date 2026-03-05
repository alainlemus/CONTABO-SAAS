<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'tax_id',
        'person_type',
        'tax_regime',
        'curp',
        'legal_rep_name',
        'legal_rep_rfc',
        'economic_activity',
        'scian_code',
        'employee_count',
        'relationship_started_at',
        'obligations_periodicity',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'status',
        'billing_cycle',
        'onboarding_notes',
        'onboarding_completed_at',
        'portal_sat_user',
        'portal_sat_password',
        'efirma_cer_path',
        'efirma_key_path',
        'documents',
        'compliance_level',
    ];

    protected $casts = [
        'onboarding_completed_at' => 'datetime',
        'relationship_started_at' => 'date',
        'documents' => 'array',
        'employee_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('owned', function (Builder $query): void {
            if (! auth()->check()) {
                return;
            }

            $query->where('user_id', auth()->user()->ownerId());
        });
    }

    // ─── Relaciones ───────────────────────────────────────────────────────────

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ClientNote::class)->latest();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class)->latest('fecha_emision');
    }
}
