<?php

namespace App\Filament\Resources\JudgeAssignments\Pages;

use App\Filament\Resources\JudgeAssignments\JudgeAssignmentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditJudgeAssignment extends EditRecord
{
    protected static string $resource = JudgeAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
