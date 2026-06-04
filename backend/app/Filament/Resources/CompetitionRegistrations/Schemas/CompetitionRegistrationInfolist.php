<?php

namespace App\Filament\Resources\CompetitionRegistrations\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CompetitionRegistrationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('competition.title')
                    ->label('Competition'),
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('registration_number'),
                IconEntry::make('rules_accepted')
                    ->boolean(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
