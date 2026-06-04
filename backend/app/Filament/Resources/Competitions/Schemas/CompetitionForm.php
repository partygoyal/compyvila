<?php

namespace App\Filament\Resources\Competitions\Schemas;

use App\Models\CompetitionCategory;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CompetitionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('competition_category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('title')
                    ->required()
                    ->live()
                    ->afterStateUpdated(
                        fn (Set $set, ?string $state) =>
                        $set('slug', Str::slug($state))
                    ),

                TextInput::make('slug')
                    ->required()
                    ->helperText('Auto generated from title, but can be edited'),

                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('rules')
                    ->columnSpanFull(),

                FileUpload::make('banner')
                    ->directory('competitions')
                    ->disk('public')
                    ->image()
                    ->imageEditor(),

                TextInput::make('entry_fee')
                    ->label('Entry Fee')
                    ->numeric()
                    ->default(0),

                TextInput::make('max_participants')
                    ->numeric(),

                TextInput::make('min_age')
                    ->numeric(),

                TextInput::make('max_age')
                    ->numeric(),

                Select::make('gender')
                    ->options([
                        'all' => 'All',
                        'male' => 'Male',
                        'female' => 'Female',
                    ])
                    ->default('all'),

                TextInput::make('country')
                    ->default('India')
                    ->required(),

                TextInput::make('state'),

                TextInput::make('district'),

                TextInput::make('city'),

                Select::make('submission_type')
                    ->options([
                        'image' => 'Image',
                        'video' => 'Video',
                        'audio' => 'Audio',
                        'document' => 'Document',
                    ])
                    ->required(),

                Select::make('total_rounds')
                    ->options([
                        1 => '1 Round',
                        2 => '2 Rounds',
                        3 => '3 Rounds',
                    ])
                    ->default(1)
                    ->required(),

                DateTimePicker::make('registration_start')
                    ->required(),

                DateTimePicker::make('registration_end')
                    ->required(),

                DateTimePicker::make('competition_start')
                    ->required(),

                DateTimePicker::make('competition_end')
                    ->required(),

                Toggle::make('allow_public_voting')
                    ->default(false),

                Toggle::make('show_scores')
                    ->default(false),

                Toggle::make('certificate_enabled')
                    ->default(true),

                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('draft')
                    ->required(),

            ]);
    }
}