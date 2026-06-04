<?php

namespace App\Filament\Resources\CompetitionRounds\Pages;

use App\Filament\Resources\CompetitionRounds\CompetitionRoundResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCompetitionRound extends EditRecord
{
    protected static string $resource = CompetitionRoundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
