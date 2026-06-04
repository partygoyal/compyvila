<?php

namespace App\Filament\Resources\JudgeAssignments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class JudgeAssignmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextEntry::make('competition.title')
                    ->label('Competition'),

                TextEntry::make('round.title')
                    ->label('Round'),

                TextEntry::make('judge.name')
                    ->label('Judge'),

                TextEntry::make('created_at')
                    ->label('Assigned On')
                    ->dateTime(),

                TextEntry::make('updated_at')
                    ->dateTime(),

            ]);
    }
}