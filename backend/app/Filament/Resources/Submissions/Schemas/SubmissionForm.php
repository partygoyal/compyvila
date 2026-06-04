<?php

namespace App\Filament\Resources\Submissions\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SubmissionForm
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

                Select::make('competition_round_id')
                    ->label('Round')
                    ->relationship('round', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('user_id')
                    ->label('Participant')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('title')
                    ->maxLength(255),

                Textarea::make('description')
                    ->columnSpanFull(),

                FileUpload::make('file_path')
                    ->label('Submission File')
                    ->disk('public')
                    ->directory('submissions')
                    ->required(),

                FileUpload::make('thumbnail')
                    ->disk('public')
                    ->directory('submission-thumbnails')
                    ->image(),

                Select::make('file_type')
                    ->options([
                        'image' => 'Image',
                        'video' => 'Video',
                        'audio' => 'Audio',
                        'document' => 'Document',
                    ])
                    ->required(),

                Select::make('status')
                    ->options([
                        'submitted' => 'Submitted',
                        'shortlisted' => 'Shortlisted',
                        'rejected' => 'Rejected',
                        'winner' => 'Winner',
                    ])
                    ->default('submitted')
                    ->required(),

            ]);
    }
}