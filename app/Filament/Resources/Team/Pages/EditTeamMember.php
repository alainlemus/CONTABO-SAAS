<?php

namespace App\Filament\Resources\Team\Pages;

use App\Filament\Resources\Team\TeamMemberResource;
use Filament\Resources\Pages\EditRecord;

class EditTeamMember extends EditRecord
{
    protected static string $resource = TeamMemberResource::class;
}
