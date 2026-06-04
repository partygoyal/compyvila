<?php

namespace App\Filament\Resources\Reviews\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('submission.title')
                    ->label('Submission')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('judge.name')
                    ->label('Judge')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('creativity'),

                TextColumn::make('presentation'),

                TextColumn::make('quality'),

                TextColumn::make('performance'),

                TextColumn::make('total_score')
                    ->badge()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Reviewed On')
                    ->dateTime()
                    ->sortable(),

            ]);
    }
}
