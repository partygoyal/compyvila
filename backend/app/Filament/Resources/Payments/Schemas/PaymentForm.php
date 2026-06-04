<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('user_id')
                    ->label('Participant')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('competition_id')
                    ->label('Competition')
                    ->relationship('competition', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('amount')
                    ->numeric()
                    ->prefix('₹')
                    ->required(),

                Select::make('payment_gateway')
                    ->options([
                        'razorpay' => 'Razorpay',
                        'stripe' => 'Stripe',
                        'manual' => 'Manual',
                    ])
                    ->required(),

                TextInput::make('transaction_id'),

                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ])
                    ->default('pending')
                    ->required(),

            ]);
    }
}