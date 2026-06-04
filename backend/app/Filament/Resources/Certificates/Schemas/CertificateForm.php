<?php

namespace App\Filament\Resources\Certificates\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CertificateForm
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

                Select::make('winner_id')
                    ->label('Winner Record')
                    ->relationship('winner', 'position')
                    ->searchable()
                    ->preload(),

                TextInput::make('certificate_number')
                    ->required()
                    ->helperText('Example: CERT-2026-00001'),

                Select::make('type')
                    ->options([
                        'winner' => 'Winner',
                        'runner_up' => 'Runner Up',
                        'participation' => 'Participation',
                    ])
                    ->required(),

                FileUpload::make('pdf_path')
                    ->label('Certificate PDF')
                    ->disk('public')
                    ->directory('certificates'),

                DateTimePicker::make('issued_at'),

            ]);
    }
}