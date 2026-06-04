<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    public function winners()
    {
        return response()->json(
            DB::table('reviews')
                ->join('submissions', 'reviews.submission_id', '=', 'submissions.id')
                ->join('users', 'submissions.user_id', '=', 'users.id')
                ->select(
                    'users.name',
                    'submissions.title',
                    DB::raw('AVG(reviews.total_score) as score')
                )
                ->groupBy(
                    'users.id',
                    'users.name',
                    'submissions.id',
                    'submissions.title'
                )
                ->orderByDesc('score')
                ->get()
        );
    }

    public function competitionResults(Competition $competition)
    {
        return response()->json(
            DB::table('reviews')
                ->join('submissions', 'reviews.submission_id', '=', 'submissions.id')
                ->join('users', 'submissions.user_id', '=', 'users.id')
                ->where(
                    'submissions.competition_id',
                    $competition->id
                )
                ->select(
                    'users.name',
                    'submissions.title',
                    DB::raw('AVG(reviews.total_score) as score')
                )
                ->groupBy(
                    'users.id',
                    'users.name',
                    'submissions.id',
                    'submissions.title'
                )
                ->orderByDesc('score')
                ->get()
        );
    }

    public function leaderboard($slug)
    {
        $competition = Competition::where(
            'slug',
            $slug
        )->firstOrFail();

        return DB::table('reviews')
            ->join(
                'submissions',
                'reviews.submission_id',
                '=',
                'submissions.id'
            )
            ->join(
                'users',
                'submissions.user_id',
                '=',
                'users.id'
            )
            ->where(
                'submissions.competition_id',
                $competition->id
            )
            ->select(
                'users.name as participant',
                'submissions.title as submission',
                DB::raw('SUM(reviews.total_score) as score')
            )
            ->groupBy(
                'users.name',
                'submissions.title'
            )
            ->orderByDesc('score')
            ->get()
            ->values()
            ->map(function ($item, $index) {

                return [
                    'rank' => $index + 1,
                    'participant' => $item->participant,
                    'submission' => $item->submission,
                    'score' => $item->score,
                ];

            });
    }
}