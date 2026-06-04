<?php

namespace App\Filament\Resources\CompetitionRounds;

use App\Filament\Resources\CompetitionRounds\Pages\CreateCompetitionRound;
use App\Filament\Resources\CompetitionRounds\Pages\EditCompetitionRound;
use App\Filament\Resources\CompetitionRounds\Pages\ListCompetitionRounds;
use App\Filament\Resources\CompetitionRounds\Pages\ViewCompetitionRound;
use App\Filament\Resources\CompetitionRounds\Schemas\CompetitionRoundForm;
use App\Filament\Resources\CompetitionRounds\Schemas\CompetitionRoundInfolist;
use App\Filament\Resources\CompetitionRounds\Tables\CompetitionRoundsTable;
use App\Models\CompetitionRound;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CompetitionRoundResource extends Resource
{
    protected static ?string $model = CompetitionRound::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return CompetitionRoundForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CompetitionRoundInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompetitionRoundsTable::configure($table);
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
            'index' => ListCompetitionRounds::route('/'),
            'create' => CreateCompetitionRound::route('/create'),
            'view' => ViewCompetitionRound::route('/{record}'),
            'edit' => EditCompetitionRound::route('/{record}/edit'),
        ];
    }
}
