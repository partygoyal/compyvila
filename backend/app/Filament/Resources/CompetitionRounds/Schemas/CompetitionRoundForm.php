<?php

namespace App\Filament\Resources\CompetitionRounds\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CompetitionRoundForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('competition_id')
                    ->label('Competition')
                    ->relationship('competition', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('round_number')
                    ->label('Round Number')
                    ->numeric()
                    ->required(),

                TextInput::make('title')
                    ->required()
                    ->placeholder('Round 1, Semi Final, Grand Finale'),

                Textarea::make('description')
                    ->columnSpanFull(),

                DateTimePicker::make('start_date'),

                DateTimePicker::make('end_date'),

                Toggle::make('is_final')
                    ->label('Final Round')
                    ->default(false),

            ]);
    }
}