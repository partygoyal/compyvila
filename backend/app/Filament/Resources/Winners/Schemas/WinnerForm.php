<?php

namespace App\Filament\Resources\Winners\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WinnerForm
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

                Select::make('participant_id')
                    ->label('Participant')
                    ->relationship('participant', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('position')
                    ->options([
                        'Winner' => 'Winner',
                        '1st Runner Up' => '1st Runner Up',
                        '2nd Runner Up' => '2nd Runner Up',
                        'Top 10' => 'Top 10',
                    ])
                    ->required(),

                Toggle::make('judge_recommended')
                    ->label('Judge Recommended')
                    ->default(true),

                Toggle::make('admin_approved')
                    ->label('Admin Approved')
                    ->default(false),

                Select::make('approved_by')
                    ->label('Approved By')
                    ->relationship('approver', 'name')
                    ->searchable()
                    ->preload(),

                Textarea::make('remarks')
                    ->columnSpanFull(),

            ]);
    }
}