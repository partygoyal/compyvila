<?php

namespace App\Filament\Resources\CompetitionRegistrations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CompetitionRegistrationForm
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

                Select::make('user_id')
                    ->label('Participant')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('registration_number')
                    ->readOnly()
                    ->dehydrated(false)
                    ->helperText('Automatically generated'),

                Toggle::make('rules_accepted')
                    ->label('Rules Accepted')
                    ->default(true),

                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'disqualified' => 'Disqualified',
                    ])
                    ->default('pending')
                    ->required(),

            ]);
    }
}