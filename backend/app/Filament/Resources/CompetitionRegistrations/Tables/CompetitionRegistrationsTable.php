<?php

namespace App\Filament\Resources\CompetitionRegistrations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompetitionRegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('registration_number')
                    ->label('Registration No.')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('competition.title')
                    ->label('Competition')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Participant')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('rules_accepted')
                    ->label('Rules')
                    ->boolean(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Registered On')
                    ->dateTime()
                    ->sortable(),

            ]);
    }
}
