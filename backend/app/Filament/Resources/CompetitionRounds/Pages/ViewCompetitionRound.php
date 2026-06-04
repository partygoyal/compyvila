<?php

namespace App\Filament\Resources\CompetitionRounds\Pages;

use App\Filament\Resources\CompetitionRounds\CompetitionRoundResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCompetitionRound extends ViewRecord
{
    protected static string $resource = CompetitionRoundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
