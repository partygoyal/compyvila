<?php

namespace App\Filament\Resources\Certificates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CertificatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('certificate_number')
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

                TextColumn::make('type')
                    ->badge(),

                TextColumn::make('issued_at')
                    ->dateTime()
                    ->sortable(),

            ]);
    }
}
