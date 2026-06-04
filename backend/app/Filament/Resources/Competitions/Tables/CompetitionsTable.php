<?php

namespace App\Filament\Resources\Competitions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompetitionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('competition_category_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('banner')
                    ->searchable(),
                TextColumn::make('entry_fee')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_participants')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('min_age')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_age')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('gender')
                    ->badge(),
                TextColumn::make('country')
                    ->searchable(),
                TextColumn::make('state')
                    ->searchable(),
                TextColumn::make('district')
                    ->searchable(),
                TextColumn::make('city')
                    ->searchable(),
                TextColumn::make('submission_type')
                    ->badge(),
                TextColumn::make('total_rounds')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('registration_start')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('registration_end')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('competition_start')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('competition_end')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('allow_public_voting')
                    ->boolean(),
                IconColumn::make('show_scores')
                    ->boolean(),
                IconColumn::make('certificate_enabled')
                    ->boolean(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
