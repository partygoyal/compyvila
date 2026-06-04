<?php

namespace App\Filament\Resources\Winners\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WinnersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('competition.title')
                    ->label('Competition')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('participant.name')
                    ->label('Participant')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('position')
                    ->badge(),

                IconColumn::make('judge_recommended')
                    ->boolean(),

                IconColumn::make('admin_approved')
                    ->boolean(),

                TextColumn::make('approver.name')
                    ->label('Approved By'),

                TextColumn::make('created_at')
                    ->label('Declared On')
                    ->dateTime(),

            ]);
    }
}
