<?php

namespace App\Filament\Resources\CompetitionRegistrations\Pages;

use App\Filament\Resources\CompetitionRegistrations\CompetitionRegistrationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCompetitionRegistration extends EditRecord
{
    protected static string $resource = CompetitionRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
