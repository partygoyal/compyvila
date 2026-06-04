<?php

namespace App\Filament\Resources\Winners\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WinnerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('competition.title')
                    ->label('Competition'),
                TextEntry::make('participant.name')
                    ->label('Participant'),
                TextEntry::make('position'),
                IconEntry::make('judge_recommended')
                    ->boolean(),
                IconEntry::make('admin_approved')
                    ->boolean(),
                TextEntry::make('approved_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('remarks')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
