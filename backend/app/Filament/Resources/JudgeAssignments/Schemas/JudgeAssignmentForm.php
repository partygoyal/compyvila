<?php

namespace App\Filament\Resources\JudgeAssignments\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class JudgeAssignmentForm
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

                Select::make('competition_round_id')
                    ->label('Round')
                    ->relationship('round', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('judge_id')
                    ->label('Judge')
                    ->options(
                        User::role('judge')
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

            ]);
    }
}