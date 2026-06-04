<?php

namespace App\Filament\Resources\CompetitionCategories\Pages;

use App\Filament\Resources\CompetitionCategories\CompetitionCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCompetitionCategory extends EditRecord
{
    protected static string $resource = CompetitionCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
