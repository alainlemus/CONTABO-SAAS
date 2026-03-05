<?php

namespace App\Filament\Resources\Team;

use App\Filament\Resources\Team\Pages\CreateTeamMember;
use App\Filament\Resources\Team\Pages\EditTeamMember;
use App\Filament\Resources\Team\Pages\ListTeamMembers;
use App\Filament\Resources\Team\Schemas\TeamMemberForm;
use App\Filament\Resources\Team\Tables\TeamMembersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TeamMemberResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $modelLabel = 'Miembro del equipo';

    protected static ?string $pluralModelLabel = 'Equipo';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 10;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('owner_id', auth()->id());
    }

    public static function canAccess(): bool
    {
        return filament()->auth()->user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return filament()->auth()->user()?->isAdmin() ?? false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return filament()->auth()->user()?->isAdmin() ?? false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return filament()->auth()->user()?->isAdmin() ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return filament()->auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return TeamMemberForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeamMembersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeamMembers::route('/'),
            'create' => CreateTeamMember::route('/create'),
            'edit' => EditTeamMember::route('/{record}/edit'),
        ];
    }
}
