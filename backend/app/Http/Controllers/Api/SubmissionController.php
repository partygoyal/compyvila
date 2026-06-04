<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompetitionRegistration;
use App\Models\Submission;
use App\Notifications\SubmissionReceived;
use App\Support\CompetitionProgress;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'competition_id' => ['required', 'exists:competitions,id'],
            'competition_round_id' => ['required', 'exists:competition_rounds,id'],
            'title' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'file' => ['required', 'file'],
        ]);

        $registration = CompetitionRegistration::where(
            'competition_id',
            $validated['competition_id']
        )
        ->where(
            'user_id',
            $request->user()->id
        )
        ->first();

        if (! $registration) {
            return response()->json([
                'success' => false,
                'message' => 'Please register for this competition first.',
            ], 422);
        }

        $registration->load('competition');

        $progress = CompetitionProgress::forUser(
            $registration->competition,
            $request->user()
        );

        if (! $progress['can_submit']) {
            return response()->json([
                'success' => false,
                'message' => $progress['progress_message'],
            ], 422);
        }

        if ((int) $validated['competition_round_id'] !== (int) $progress['current_round_id']) {
            return response()->json([
                'success' => false,
                'message' => "You can only submit for Round {$progress['current_round_number']} right now.",
            ], 422);
        }

        $file = $request->file('file');

        $path = $file->store(
            'submissions',
            'public'
        );

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        $fileType = match (true) {
            in_array($extension, ['jpg', 'jpeg', 'png', 'webp']) => 'image',
            in_array($extension, ['mp4', 'mov', 'avi']) => 'video',
            in_array($extension, ['mp3', 'wav']) => 'audio',
            default => 'document',
        };

        $submission = Submission::create([
            'competition_id' => $validated['competition_id'],
            'competition_round_id' => $validated['competition_round_id'],
            'user_id' => $request->user()->id,
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'] ?? null,
            'file_path' => $path,
            'file_type' => $fileType,
            'status' => 'submitted',
        ]);

        $submission->load(['competition', 'round']);
        $this->sendNotification(
            $request->user(),
            new SubmissionReceived($submission)
        );

        return response()->json([
            'success' => true,
            'message' => 'Submission uploaded successfully.',
            'submission' => $submission,
        ]);
    }

    public function show(Request $request, $id)
    {
        $submission = \App\Models\Submission::with([
            'competition',
            'round',
            'user',
            'reviews.judge',
        ])->findOrFail($id);

        if ($submission->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot view this submission.',
            ], 403);
        }

        return $submission;
    }

    private function sendNotification($user, $notification): void
    {
        try {
            $user->notify($notification);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
