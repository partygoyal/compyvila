<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->merge(['email' => strtolower(trim((string) $request->input('email', '')))]);
        $request->validate([
            'email' => ['required', 'email', 'max:190'],
            'consent' => ['accepted'],
            'website' => ['nullable', 'max:0'],
        ]);
        Subscriber::firstOrCreate(['email' => $request->input('email')], ['created_at' => now()->format('Y-m-d H:i:s')]);

        return redirect('/#newsletter')->with('flash', 'You’re on the list. Your newsletter preference has been saved.');
    }
}
