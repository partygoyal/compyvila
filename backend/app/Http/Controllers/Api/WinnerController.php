<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Winner;
use App\Models\Competition;

class WinnerController extends Controller
{
    public function competitionWinners($slug)
    {
        $competition = Competition::where(
            'slug',
            $slug
        )->firstOrFail();

        return Winner::with([
            'participant',
            'competition'
        ])
        ->where(
            'competition_id',
            $competition->id
        )
        ->orderBy('position')
        ->get();
    }
}