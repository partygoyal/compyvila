<?php

namespace App\Filament\Resources\CompetitionRegistrations;

use App\Filament\Resources\CompetitionRegistrations\Pages\CreateCompetitionRegistration;
use App\Filament\Resources\CompetitionRegistrations\Pages\EditCompetitionRegistration;
use App\Filament\Resources\CompetitionRegistrations\Pages\ListCompetitionRegistrations;
use App\Filament\Resources\CompetitionRegistrations\Pages\ViewCompetitionRegistration;
use App\Filament\Resources\CompetitionRegistrations\Schemas\CompetitionRegistrationForm;
use App\Filament\Resources\CompetitionRegistrations\Schemas\CompetitionRegistrationInfolist;
use App\Filament\Resources\CompetitionRegistrations\Tables\CompetitionRegistrationsTable;
use App\Models\CompetitionRegistration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CompetitionRegistrationResource extends Resource
{
    protected static ?string $model = CompetitionRegistration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'registration_number';

    public static function form(Schema $schema): Schema
    {
        return CompetitionRegistrationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CompetitionRegistrationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompetitionRegistrationsTable::configure($table);
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
            'index' => ListCompetitionRegistrations::route('/'),
            'create' => CreateCompetitionRegistration::route('/create'),
            'view' => ViewCompetitionRegistration::route('/{record}'),
            'edit' => EditCompetitionRegistration::route('/{record}/edit'),
        ];
    }
    
}
