<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ReviewInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('submission.title')
                    ->label('Submission'),
                TextEntry::make('judge.name')
                    ->label('Judge'),
                TextEntry::make('creativity')
                    ->numeric(),
                TextEntry::make('presentation')
                    ->numeric(),
                TextEntry::make('quality')
                    ->numeric(),
                TextEntry::make('performance')
                    ->numeric(),
                TextEntry::make('total_score')
                    ->numeric(),
                TextEntry::make('comments')
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
