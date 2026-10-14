<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Snapshot;
use Illuminate\Http\Request;

class SnapshotController extends Controller
{
    public function edit(Request $request)
    {
        $snapshot = $request->filled('id') ? Snapshot::findOrFail($request->integer('id')) : new Snapshot;

        return view('admin.snapshot', ['edit' => $snapshot->toArray()]);
    }

    public function store(Request $request)
    {
        $request->merge(['symbol' => strtoupper(trim((string) $request->input('symbol', '')))]);
        $data = $request->validate([
            'symbol' => ['required', 'regex:/^[A-Z0-9]{2,12}$/'],
            'name' => ['required', 'string', 'max:80'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999999999'],
            'change_pct' => ['required', 'numeric', 'between:-99999999,99999999'],
            'captured_at' => ['required', 'date'],
            'source' => [setting('demo', '1') === '1' ? 'nullable' : 'required', 'url:http,https', 'max:255'],
        ]);
        $data['source'] ??= '';
        $data['captured_at'] = date('Y-m-d H:i:s', strtotime($data['captured_at']));
        Snapshot::updateOrCreate(['symbol' => $data['symbol']], $data);

        return redirect('/admin?tab=market')->with('flash', 'Manual market snapshot saved.');
    }
}
