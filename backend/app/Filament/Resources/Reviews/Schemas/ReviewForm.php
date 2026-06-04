<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('submission_id')
                    ->label('Submission')
                    ->relationship('submission', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('judge_id')
                    ->label('Judge')
                    ->relationship('judge', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('creativity')
                    ->numeric()
                    ->default(0)
                    ->required(),

                TextInput::make('presentation')
                    ->numeric()
                    ->default(0)
                    ->required(),

                TextInput::make('quality')
                    ->numeric()
                    ->default(0)
                    ->required(),

                TextInput::make('performance')
                    ->numeric()
                    ->default(0)
                    ->required(),

                TextInput::make('total_score')
                    ->numeric()
                    ->readOnly()
                    ->helperText('Will be calculated automatically'),

                Textarea::make('comments')
                    ->columnSpanFull(),

            ]);
    }
}