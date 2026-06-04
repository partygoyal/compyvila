<?php

namespace App\Filament\Resources\CompetitionRounds\Pages;

use App\Filament\Resources\CompetitionRounds\CompetitionRoundResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompetitionRounds extends ListRecords
{
    protected static string $resource = CompetitionRoundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
