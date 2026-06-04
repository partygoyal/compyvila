<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\PublicVote;
use App\Models\Submission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class PublicVoteController extends Controller
{
    public function submissions(Request $request, string $slug)
    {
        $competition = Competition::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        if (! $competition->allow_public_voting) {
            return response()->json([
                'success' => false,
                'message' => 'Public voting is not enabled for this competition.',
            ], 422);
        }

        $user = $this->optionalUser($request);

        $submissions = Submission::with('user')
            ->withCount([
                'publicVotes as public_up_votes_count' => fn ($query) =>
                    $query->where('vote_type', 'up'),
                'publicVotes as public_down_votes_count' => fn ($query) =>
                    $query->where('vote_type', 'down'),
            ])
            ->when($user, fn ($query) =>
                $query->with([
                    'publicVotes' => fn ($voteQuery) =>
                        $voteQuery
                            ->where('user_id', $user->id)
                            ->select([
                                'id',
                                'submission_id',
                                'vote_type',
                            ]),
                ])
            )
            ->where('competition_id', $competition->id)
            ->whereIn('status', [
                'submitted',
                'shortlisted',
                'winner',
            ])
            ->latest()
            ->get()
            ->sortByDesc(fn ($submission) =>
                $submission->public_up_votes_count
                - $submission->public_down_votes_count
            )
            ->values();

        return response()->json([
            'success' => true,
            'competition' => $competition,
            'can_vote' => (bool) $user,
            'submissions' => $submissions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'submission_id' => ['required', 'exists:submissions,id'],
            'vote_type' => ['required', 'in:up,down'],
        ]);

        $submission = Submission::with('competition')
            ->findOrFail($validated['submission_id']);

        $votingError = $this->votingError($submission);

        if ($votingError) {
            return response()->json([
                'success' => false,
                'message' => $votingError,
            ], 422);
        }

        $vote = PublicVote::updateOrCreate(
            [
                'competition_id' => $submission->competition_id,
                'submission_id' => $submission->id,
                'user_id' => $request->user()->id,
            ],
            [
                'voter_hash' => $this->voterHash($request),
                'vote_type' => $validated['vote_type'],
            ]
        );

        $submission->loadCount([
            'publicVotes as public_up_votes_count' => fn ($query) =>
                $query->where('vote_type', 'up'),
            'publicVotes as public_down_votes_count' => fn ($query) =>
                $query->where('vote_type', 'down'),
        ]);

        return response()->json([
            'success' => true,
            'message' => $vote->wasRecentlyCreated
                ? 'Vote recorded successfully.'
                : 'Vote updated successfully.',
            'vote_type' => $vote->vote_type,
            'submission' => $submission,
        ]);
    }

    public function leaderboard(string $slug)
    {
        $competition = Competition::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        if (! $competition->allow_public_voting) {
            return response()->json([
                'success' => false,
                'message' => 'Public voting is not enabled for this competition.',
            ], 422);
        }

        $submissions = Submission::with('user')
            ->withCount([
                'publicVotes as public_up_votes_count' => fn ($query) =>
                    $query->where('vote_type', 'up'),
                'publicVotes as public_down_votes_count' => fn ($query) =>
                    $query->where('vote_type', 'down'),
            ])
            ->where('competition_id', $competition->id)
            ->get()
            ->sortByDesc(fn (Submission $submission) =>
                $submission->public_up_votes_count
                - $submission->public_down_votes_count
            )
            ->values()
            ->map(function (Submission $submission, int $index) {
                return [
                    'rank' => $index + 1,
                    'submission_id' => $submission->id,
                    'participant' => $submission->user?->name,
                    'submission' => $submission->title,
                    'up_votes' => $submission->public_up_votes_count,
                    'down_votes' => $submission->public_down_votes_count,
                    'net_votes' => $submission->public_up_votes_count
                        - $submission->public_down_votes_count,
                ];
            });

        return response()->json([
            'success' => true,
            'competition' => $competition,
            'leaderboard' => $submissions,
        ]);
    }

    private function votingError(Submission $submission): ?string
    {
        $competition = $submission->competition;
        $now = Carbon::now();

        if ($competition->status !== 'published') {
            return 'This competition is not open for public voting.';
        }

        if (! $competition->allow_public_voting) {
            return 'Public voting is not enabled for this competition.';
        }

        if ($competition->competition_start && $now->lt(
            Carbon::parse($competition->competition_start)
        )) {
            return 'Public voting has not started yet.';
        }

        if ($competition->competition_end && $now->gt(
            Carbon::parse($competition->competition_end)
        )) {
            return 'Public voting has ended.';
        }

        if (! in_array($submission->status, [
            'submitted',
            'shortlisted',
            'winner',
        ], true)) {
            return 'This submission is not available for public voting.';
        }

        return null;
    }

    private function voterHash(Request $request): string
    {
        return hash(
            'sha256',
            implode('|', [
                $request->ip(),
                $request->userAgent(),
                config('app.key'),
            ])
        );
    }

    private function optionalUser(Request $request)
    {
        if ($request->user()) {
            return $request->user();
        }

        $token = $request->bearerToken();

        if (! $token) {
            return null;
        }

        return PersonalAccessToken::findToken($token)?->tokenable;
    }
}
