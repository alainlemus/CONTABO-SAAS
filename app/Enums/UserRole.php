<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Capturista = 'capturista';
    case Viewer = 'viewer';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Capturista => 'Capturista',
            self::Viewer => 'Solo lectura',
        };
    }

    public function canCreate(): bool
    {
        return match ($this) {
            self::Admin, self::Capturista => true,
            self::Viewer => false,
        };
    }

    public function canEdit(): bool
    {
        return match ($this) {
            self::Admin, self::Capturista => true,
            self::Viewer => false,
        };
    }

    public function canDelete(): bool
    {
        return $this === self::Admin;
    }

    public function canManageTeam(): bool
    {
        return $this === self::Admin;
    }
}
