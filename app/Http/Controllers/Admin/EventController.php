<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    public function edit(Request $request)
    {
        $event = $request->filled('id') ? Event::findOrFail($request->integer('id')) : new Event;

        return view('admin.event', ['edit' => $event->toArray()]);
    }

    public function store(Request $request)
    {
        $event = $request->integer('id') ? Event::findOrFail($request->integer('id')) : new Event;
        $data = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'category' => ['required', Rule::in(['Editorial', 'Learn', 'Upgrade', 'Unlock', 'Economic', 'Conference'])],
            'event_at' => ['required', 'date'],
            'source' => ['nullable', 'url:http,https', 'max:255'],
        ]);
        $data['source'] ??= '';
        $data['event_at'] = date('Y-m-d H:i:s', strtotime($data['event_at']));
        $data['verified_at'] = now()->format('Y-m-d H:i:s');
        $event->fill($data)->save();

        return redirect('/admin?tab=events')->with('flash', 'Calendar event saved and verification date updated.');
    }
}
