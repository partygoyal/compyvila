<?php

namespace App\Filament\Resources\JudgeAssignments\Pages;

use App\Filament\Resources\JudgeAssignments\JudgeAssignmentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewJudgeAssignment extends ViewRecord
{
    protected static string $resource = JudgeAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
