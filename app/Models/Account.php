<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    /** @use HasFactory<\Database\Factories\AccountFactory> */
    use HasFactory;

    protected $fillable = [
        'firm_id',
        'code',
        'name',
        'description',
        'type',
        'category',
        'is_major',
        'parent_id',
        'currency',
        'level',
        'is_active',
    ];

    protected $casts = [
        'is_major' => 'bool',
        'is_active' => 'bool',
    ];

    /**
     * @return BelongsTo<Firm, Account>
     */
    public function firm(): BelongsTo
    {
        return $this->belongsTo(Firm::class);
    }

    /**
     * @return BelongsTo<Account, Account>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
