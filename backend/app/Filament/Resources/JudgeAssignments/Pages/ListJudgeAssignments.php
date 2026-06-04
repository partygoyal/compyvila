<?php

namespace App\Filament\Resources\JudgeAssignments\Pages;

use App\Filament\Resources\JudgeAssignments\JudgeAssignmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJudgeAssignments extends ListRecords
{
    protected static string $resource = JudgeAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
