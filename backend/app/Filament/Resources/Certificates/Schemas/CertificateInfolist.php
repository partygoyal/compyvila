<?php

namespace App\Filament\Resources\Certificates\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CertificateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('competition.title')
                    ->label('Competition'),
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('winner.id')
                    ->label('Winner')
                    ->placeholder('-'),
                TextEntry::make('certificate_number'),
                TextEntry::make('type')
                    ->badge(),
                TextEntry::make('pdf_path')
                    ->placeholder('-'),
                TextEntry::make('issued_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
