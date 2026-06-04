<?php

namespace App\Support;

use App\Models\Competition;
use App\Models\Submission;
use App\Models\User;

class CompetitionProgress
{
    public static function forUser(
        Competition $competition,
        User $user
    ): array {
        $rounds = $competition->rounds()
            ->orderBy('round_number')
            ->get();

        if ($rounds->isEmpty()) {
            return [
                'current_round' => null,
                'current_round_id' => null,
                'current_round_number' => null,
                'progress_status' => 'no_rounds',
                'progress_message' => 'No rounds are configured for this competition.',
                'can_submit' => false,
            ];
        }

        $submissions = Submission::with('round')
            ->where('competition_id', $competition->id)
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('competition_round_id');

        foreach ($rounds as $round) {
            $submission = $submissions->get($round->id);

            if (! $submission) {
                return [
                    'current_round' => $round,
                    'current_round_id' => $round->id,
                    'current_round_number' => $round->round_number,
                    'progress_status' => $round->round_number === 1
                        ? 'ready_for_round'
                        : 'promoted_to_round',
                    'progress_message' => "You are currently in Round {$round->round_number}.",
                    'can_submit' => true,
                ];
            }

            if ($submission->status !== 'shortlisted') {
                return [
                    'current_round' => $round,
                    'current_round_id' => $round->id,
                    'current_round_number' => $round->round_number,
                    'progress_status' => 'under_review',
                    'progress_message' => "Your Round {$round->round_number} submission is under review.",
                    'can_submit' => false,
                ];
            }
        }

        $finalRound = $rounds->last();

        return [
            'current_round' => $finalRound,
            'current_round_id' => $finalRound->id,
            'current_round_number' => $finalRound->round_number,
            'progress_status' => 'completed',
            'progress_message' => 'You have completed all configured rounds.',
            'can_submit' => false,
        ];
    }
}
