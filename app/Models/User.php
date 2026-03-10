<?php

namespace App\Models;

use App\Enums\UserRole;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Cashier\Billable;

class User extends Authenticatable implements CanResetPasswordContract, FilamentUser, HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Billable, CanResetPassword, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'avatar_url',
        'password',
        'role',
        'owner_id',
        'is_active',
        'trial_ends_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
            'trial_ends_at' => 'datetime',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url
            ? Storage::disk('public')->url($this->avatar_url)
            : null;
    }

    // ─── Roles ────────────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isCapturista(): bool
    {
        return $this->role === UserRole::Capturista;
    }

    public function isViewer(): bool
    {
        return $this->role === UserRole::Viewer;
    }

    /**
     * Devuelve el ID del admin dueño de la cuenta.
     * Si el usuario ya es admin, devuelve su propio ID.
     */
    public function ownerId(): int
    {
        return $this->owner_id ?? $this->id;
    }

    // ─── Relaciones ───────────────────────────────────────────────────────────

    /** El admin que creó a este usuario (null si es admin). */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /** Usuarios del equipo que este admin ha creado. */
    public function teamMembers(): HasMany
    {
        return $this->hasMany(User::class, 'owner_id');
    }

    /** Clientes que pertenecen a este admin. */
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'user_id');
    }
}
