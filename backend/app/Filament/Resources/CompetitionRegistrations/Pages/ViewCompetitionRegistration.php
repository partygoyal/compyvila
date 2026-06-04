<?php

namespace App\Filament\Resources\CompetitionRegistrations\Pages;

use App\Filament\Resources\CompetitionRegistrations\CompetitionRegistrationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCompetitionRegistration extends ViewRecord
{
    protected static string $resource = CompetitionRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
