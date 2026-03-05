<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Firm extends Model
{
    /** @use HasFactory<\Database\Factories\FirmFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'tax_id',
        'contact_email',
        'contact_phone',
        'billing_plan',
        'billing_expires_at',
    ];

    protected $casts = [
        'billing_expires_at' => 'date',
        'settings' => 'array',
    ];

    /**
     * @return HasMany<Client>
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    /**
     * @return HasMany<Account>
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }
}
