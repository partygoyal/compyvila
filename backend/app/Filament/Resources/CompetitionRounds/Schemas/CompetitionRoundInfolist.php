<?php

namespace App\Filament\Resources\CompetitionRounds\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CompetitionRoundInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextEntry::make('competition.title')
                    ->label('Competition'),

                TextEntry::make('round_number')
                    ->label('Round Number'),

                TextEntry::make('title'),

                TextEntry::make('description')
                    ->placeholder('-'),

                IconEntry::make('is_final')
                    ->label('Final Round')
                    ->boolean(),

                TextEntry::make('start_date')
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('end_date')
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('created_at')
                    ->dateTime(),

                TextEntry::make('updated_at')
                    ->dateTime(),

            ]);
    }
}