<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CompetitionRegistration;
use App\Models\Submission;
use App\Support\CompetitionProgress;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function myRegistrations(Request $request)
    {
        $registrations = CompetitionRegistration::with([
                'competition.rounds',
            ])
                ->where('user_id', $request->user()->id)
                ->latest()
                ->get()
                ->map(function ($registration) use ($request) {
                    $progress = CompetitionProgress::forUser(
                        $registration->competition,
                        $request->user()
                    );

                    return [
                        ...$registration->toArray(),
                        'current_round' => $progress['current_round'],
                        'current_round_id' => $progress['current_round_id'],
                        'current_round_number' => $progress['current_round_number'],
                        'progress_status' => $progress['progress_status'],
                        'progress_message' => $progress['progress_message'],
                        'can_submit' => $progress['can_submit'],
                    ];
                });

        return response()->json($registrations);
    }

    public function mySubmissions(Request $request)
    {
        return response()->json(
            Submission::with([
                'competition',
                'round',
                'reviews.judge',
            ])
                ->where('user_id', $request->user()->id)
                ->latest()
                ->get()
        );
    }

    public function myCertificates(Request $request)
    {
        return Certificate::with([
            'competition',
            'winner'
        ])
        ->where('user_id', $request->user()->id)
        ->get();
    }
}
