<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Article, Enquiry, Event, Snapshot, Subscriber};
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $tab = $request->query('tab', 'overview');
        abort_unless(in_array($tab, ['overview', 'articles', 'market', 'events', 'subscribers', 'enquiries', 'settings']), 404);

        return view('admin.dashboard', [
            'tab' => $tab,
            'articles' => $tab === 'articles' ? Article::latest('published_at')->get() : collect(),
            'snapshots' => $tab === 'market' ? Snapshot::orderBy('id')->get() : collect(),
            'events' => $tab === 'events' ? Event::orderBy('event_at')->get() : collect(),
            'subscribers' => $tab === 'subscribers' ? Subscriber::latest('created_at')->get() : collect(),
            'enquiries' => $tab === 'enquiries' ? Enquiry::latest('created_at')->get() : collect(),
            'counts' => ['articles' => Article::count(), 'events' => Event::count(), 'subscribers' => Subscriber::count(), 'enquiries' => Enquiry::count()],
        ]);
    }
}
