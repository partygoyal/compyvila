<?php

namespace App\Filament\Resources\CompetitionCategories\Pages;

use App\Filament\Resources\CompetitionCategories\CompetitionCategoryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCompetitionCategory extends ViewRecord
{
    protected static string $resource = CompetitionCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
