<?php

namespace App\Filament\Resources\CompetitionCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CompetitionCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(
                        fn (Set $set, ?string $state) =>
                        $set('slug', \Illuminate\Support\Str::slug($state))
                    ),

                TextInput::make('slug')
                    ->required()
                    ->readOnly(),

                Textarea::make('description'),

                FileUpload::make('icon')
                    ->directory('categories'),

                Toggle::make('status')
                    ->default(true),

            ]);
    }
}