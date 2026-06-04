<?php

namespace App\Filament\Resources\CompetitionRegistrations\Pages;

use App\Filament\Resources\CompetitionRegistrations\CompetitionRegistrationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompetitionRegistrations extends ListRecords
{
    protected static string $resource = CompetitionRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
