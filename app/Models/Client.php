<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory;

    protected $fillable = [
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

    public function notes(): HasMany
    {
        return $this->hasMany(ClientNote::class)->latest();
    }
}
