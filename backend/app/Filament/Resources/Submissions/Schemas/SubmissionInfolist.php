<?php

namespace App\Filament\Resources\Submissions\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubmissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextEntry::make('competition.title')
                    ->label('Competition'),

                TextEntry::make('round.title')
                    ->label('Round'),

                TextEntry::make('user.name')
                    ->label('Participant'),

                TextEntry::make('title'),

                TextEntry::make('description')
                    ->placeholder('-'),

                TextEntry::make('file_type'),

                TextEntry::make('status'),

                TextEntry::make('file_path'),

                TextEntry::make('created_at')
                    ->dateTime(),

                Section::make('Judge Reviews')
                    ->schema([
                        TextEntry::make('judge_review_summary')
                            ->label('Review Summary')
                            ->state(function ($record): string {
                                $reviews = $record->reviews;

                                if ($reviews->isEmpty()) {
                                    return 'No judge reviews yet.';
                                }

                                return sprintf(
                                    'Reviews: %d | Average Score: %s/40',
                                    $reviews->count(),
                                    round($reviews->avg('total_score'), 2)
                                );
                            }),

                        RepeatableEntry::make('reviews')
                            ->label('All Judge Reviews')
                            ->placeholder('No judge reviews yet.')
                            ->table([
                                TableColumn::make('Judge'),
                                TableColumn::make('Creativity'),
                                TableColumn::make('Presentation'),
                                TableColumn::make('Quality'),
                                TableColumn::make('Performance'),
                                TableColumn::make('Total'),
                                TableColumn::make('Comments'),
                                TableColumn::make('Reviewed On'),
                            ])
                            ->schema([
                                TextEntry::make('judge.name')
                                    ->label('Judge')
                                    ->placeholder('-'),
                                TextEntry::make('creativity')
                                    ->numeric(),
                                TextEntry::make('presentation')
                                    ->numeric(),
                                TextEntry::make('quality')
                                    ->numeric(),
                                TextEntry::make('performance')
                                    ->numeric(),
                                TextEntry::make('total_score')
                                    ->badge()
                                    ->numeric(),
                                TextEntry::make('comments')
                                    ->placeholder('-')
                                    ->limit(80),
                                TextEntry::make('created_at')
                                    ->dateTime(),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('Public Voting')
                    ->schema([
                        TextEntry::make('public_vote_summary')
                            ->label('Vote Summary')
                            ->state(function ($record): string {
                                $upVotes = $record->publicVotes
                                    ->where('vote_type', 'up')
                                    ->count();
                                $downVotes = $record->publicVotes
                                    ->where('vote_type', 'down')
                                    ->count();

                                return sprintf(
                                    'Up: %d | Down: %d | Net: %d',
                                    $upVotes,
                                    $downVotes,
                                    $upVotes - $downVotes
                                );
                            }),

                        RepeatableEntry::make('publicVotes')
                            ->label('All Public Votes')
                            ->placeholder('No public votes yet.')
                            ->table([
                                TableColumn::make('User'),
                                TableColumn::make('Vote'),
                                TableColumn::make('Voted On'),
                            ])
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('User')
                                    ->placeholder('-'),
                                TextEntry::make('vote_type')
                                    ->label('Vote')
                                    ->badge()
                                    ->formatStateUsing(
                                        fn (string $state): string =>
                                            $state === 'up'
                                                ? 'Thumbs Up'
                                                : 'Thumbs Down'
                                    )
                                    ->color(
                                        fn (string $state): string =>
                                            $state === 'up'
                                                ? 'success'
                                                : 'danger'
                                    ),
                                TextEntry::make('created_at')
                                    ->dateTime(),
                            ]),
                    ])
                    ->columnSpanFull(),

            ]);
    }
}
