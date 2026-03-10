<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Models\Client;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    protected static ?string $navigationLabel = 'Panel KPIs';

    public static function canAccess(): bool
    {
        return filament()->auth()->user()?->isAdmin() ?? false;
    }

    public function mountCanAuthorizeAccess(): void
    {
        if (! static::canAccess()) {
            $this->redirect(ClientResource::getUrl('index'));
        }
    }

    protected static ?string $title = 'Panel KPIs';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    public function filtersForm(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('client_id')
                ->label('Filtrar por cliente')
                ->placeholder('Todos los clientes')
                ->options(fn (): array => Client::query()
                    ->where('status', 'active')
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all()
                )
                ->native(false)
                ->searchable(),
        ]);
    }

    /**
     * Returns the active client_id filter from the Dashboard session.
     * Widgets call this to scope their queries.
     */
    public static function getActiveClientId(): ?int
    {
        $key = md5(static::class).'_filters';
        $filters = session()->get($key);

        $clientId = $filters['client_id'] ?? null;

        return $clientId ? (int) $clientId : null;
    }
}
