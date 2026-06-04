<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionCategory;

class CompetitionController extends Controller
{
    public function categories()
    {
        return response()->json(
            CompetitionCategory::where('status', true)
                ->orderBy('name')
                ->get()
        );
    }

    public function competitions()
    {
        return response()->json(
            Competition::with('category')
                ->where('status', 'published')
                ->latest()
                ->get()
        );
    }

    public function show(string $slug)
    {
        $competition = Competition::with([
            'category',
            'rounds',
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($competition);
    }
}