<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientNote extends Model
{
    /** @use HasFactory<\Database\Factories\ClientNoteFactory> */
    use HasFactory;

    protected $fillable = [
        'client_id',
        'user_id',
        'type',
        'body',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
