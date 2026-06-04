<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JudgeAssignment;
use App\Models\Review;
use App\Models\Submission;
use App\Notifications\SubmissionReviewed;
use App\Notifications\SubmissionShortlistStatusChanged;
use Illuminate\Http\Request;

class JudgeController extends Controller
{
    public function assignments(Request $request)
    {
        return response()->json(
            JudgeAssignment::with([
                'competition',
                'round'
            ])
            ->where('judge_id', $request->user()->id)
            ->get()
        );
    }

    public function submissions(Request $request)
    {
        $roundIds = JudgeAssignment::where(
            'judge_id',
            $request->user()->id
        )->pluck('competition_round_id');

        return response()->json(
            Submission::with([
                'competition',
                'round',
                'user',
                'reviews' => fn ($query) =>
                    $query->where('judge_id', $request->user()->id),
            ])
            ->whereIn(
                'competition_round_id',
                $roundIds
            )
            ->get()
        );
    }

    public function review(Request $request)
    {
        $validated = $request->validate([
            'submission_id' => ['required', 'exists:submissions,id'],
            'creativity' => ['required', 'integer', 'min:0', 'max:10'],
            'presentation' => ['required', 'integer', 'min:0', 'max:10'],
            'quality' => ['required', 'integer', 'min:0', 'max:10'],
            'performance' => ['required', 'integer', 'min:0', 'max:10'],
            'comments' => ['nullable'],
        ]);

        $totalScore =
            $validated['creativity']
            + $validated['presentation']
            + $validated['quality']
            + $validated['performance'];

        $submission = Submission::findOrFail($validated['submission_id']);

        $assigned = JudgeAssignment::where(
            'judge_id',
            $request->user()->id
        )
        ->where(
            'competition_round_id',
            $submission->competition_round_id
        )
        ->exists();

        if (! $assigned) {
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to review this submission.',
            ], 403);
        }

        $review = Review::updateOrCreate(
            [
                'submission_id' => $validated['submission_id'],
                'judge_id' => $request->user()->id,
            ],
            [
                'creativity' => $validated['creativity'],
                'presentation' => $validated['presentation'],
                'quality' => $validated['quality'],
                'performance' => $validated['performance'],
                'total_score' => $totalScore,
                'comments' => $validated['comments'] ?? null,
            ]
        );

        $review->load([
            'judge',
            'submission.competition',
        ]);

        $this->sendNotification(
            $review->submission->user,
            new SubmissionReviewed($review)
        );

        return response()->json([
            'success' => true,
            'review' => $review,
        ]);
    }

    public function shortlist(Request $request, Submission $submission)
    {
        $validated = $request->validate([
            'shortlisted' => ['required', 'boolean'],
        ]);

        if (! $this->judgeCanReviewSubmission($request, $submission)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to shortlist this submission.',
            ], 403);
        }

        $statusChanged = false;

        if ($validated['shortlisted'] && $submission->status !== 'shortlisted') {
            $submission->update([
                'status' => 'shortlisted',
            ]);
            $statusChanged = true;
        } elseif ($submission->status === 'shortlisted') {
            $submission->update([
                'status' => 'submitted',
            ]);
            $statusChanged = true;
        }

        if ($statusChanged) {
            $submission->load([
                'competition',
                'round',
                'user',
            ]);

            $this->sendNotification(
                $submission->user,
                new SubmissionShortlistStatusChanged(
                    $submission,
                    $validated['shortlisted']
                )
            );
        }

        return response()->json([
            'success' => true,
            'submission' => $submission->fresh([
                'competition',
                'round',
                'user',
                'reviews' => fn ($query) =>
                    $query->where('judge_id', $request->user()->id),
            ]),
        ]);
    }

    private function sendNotification($user, $notification): void
    {
        try {
            $user->notify($notification);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    private function judgeCanReviewSubmission(
        Request $request,
        Submission $submission
    ): bool {
        return JudgeAssignment::where(
            'judge_id',
            $request->user()->id
        )
        ->where(
            'competition_round_id',
            $submission->competition_round_id
        )
        ->exists();
    }
}
