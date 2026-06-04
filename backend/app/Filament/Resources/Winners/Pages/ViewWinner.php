<?php

namespace App\Filament\Resources\Winners\Pages;

use App\Filament\Resources\Winners\WinnerResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewWinner extends ViewRecord
{
    protected static string $resource = WinnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
