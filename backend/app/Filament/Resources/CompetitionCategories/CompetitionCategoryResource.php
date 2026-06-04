<?php

namespace App\Filament\Resources\CompetitionCategories;

use App\Filament\Resources\CompetitionCategories\Pages\CreateCompetitionCategory;
use App\Filament\Resources\CompetitionCategories\Pages\EditCompetitionCategory;
use App\Filament\Resources\CompetitionCategories\Pages\ListCompetitionCategories;
use App\Filament\Resources\CompetitionCategories\Pages\ViewCompetitionCategory;
use App\Filament\Resources\CompetitionCategories\Schemas\CompetitionCategoryForm;
use App\Filament\Resources\CompetitionCategories\Schemas\CompetitionCategoryInfolist;
use App\Filament\Resources\CompetitionCategories\Tables\CompetitionCategoriesTable;
use App\Models\CompetitionCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CompetitionCategoryResource extends Resource
{
    protected static ?string $model = CompetitionCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'CompetitionCategory';

    public static function form(Schema $schema): Schema
    {
        return CompetitionCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CompetitionCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompetitionCategoriesTable::configure($table);
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
            'index' => ListCompetitionCategories::route('/'),
            'create' => CreateCompetitionCategory::route('/create'),
            'view' => ViewCompetitionCategory::route('/{record}'),
            'edit' => EditCompetitionCategory::route('/{record}/edit'),
        ];
    }

}
