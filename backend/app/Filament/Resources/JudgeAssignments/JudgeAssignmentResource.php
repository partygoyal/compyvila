<?php

namespace App\Filament\Resources\JudgeAssignments;

use App\Filament\Resources\JudgeAssignments\Pages\CreateJudgeAssignment;
use App\Filament\Resources\JudgeAssignments\Pages\EditJudgeAssignment;
use App\Filament\Resources\JudgeAssignments\Pages\ListJudgeAssignments;
use App\Filament\Resources\JudgeAssignments\Pages\ViewJudgeAssignment;
use App\Filament\Resources\JudgeAssignments\Schemas\JudgeAssignmentForm;
use App\Filament\Resources\JudgeAssignments\Schemas\JudgeAssignmentInfolist;
use App\Filament\Resources\JudgeAssignments\Tables\JudgeAssignmentsTable;
use App\Models\JudgeAssignment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JudgeAssignmentResource extends Resource
{
    protected static ?string $model = JudgeAssignment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return JudgeAssignmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return JudgeAssignmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JudgeAssignmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJudgeAssignments::route('/'),
            'create' => CreateJudgeAssignment::route('/create'),
            'view' => ViewJudgeAssignment::route('/{record}'),
            'edit' => EditJudgeAssignment::route('/{record}/edit'),
        ];
    }

}
