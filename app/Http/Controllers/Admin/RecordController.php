<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Article, Enquiry, Event, Snapshot, Subscriber};
use Illuminate\Support\Facades\Storage;

class RecordController extends Controller
{
    public function destroy(string $type, int $id)
    {
        $models = ['articles' => Article::class, 'events' => Event::class, 'snapshots' => Snapshot::class, 'subscribers' => Subscriber::class, 'enquiries' => Enquiry::class];
        abort_unless(isset($models[$type]), 404);
        $record = $models[$type]::findOrFail($id);
        $image = $type === 'articles' ? $record->image : null;
        $record->delete();
        if ($image) {
            Storage::disk('editorial')->delete(basename($image));
        }

        return redirect('/admin?tab='.($type === 'snapshots' ? 'market' : $type))->with('flash', 'Record deleted.');
    }
}
