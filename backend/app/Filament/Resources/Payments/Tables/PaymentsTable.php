<?php

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('user.name')
                    ->label('Participant')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('competition.title')
                    ->label('Competition')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->money('INR')
                    ->sortable(),

                TextColumn::make('payment_gateway')
                    ->badge(),

                TextColumn::make('transaction_id')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Paid On')
                    ->dateTime()
                    ->sortable(),

            ]);
    }
}
