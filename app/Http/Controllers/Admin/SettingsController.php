<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash};

class SettingsController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'brief_title' => ['required', 'string', 'max:255'],
            'brief_body' => ['required', 'string', 'max:10000'],
            'brief_points' => ['required', 'string', 'max:10000'],
            'media_audience' => ['required', 'string', 'max:10000'],
            'media_email' => [$request->boolean('demo') ? 'nullable' : 'required', 'email', 'max:190'],
        ]);
        DB::transaction(function () use ($data, $request) {
            foreach ($data as $name => $value) {
                Setting::put($name, $value ?? '');
            }
            Setting::put('demo', $request->boolean('demo') ? '1' : '0');
            Setting::put('brief_updated', now()->format('Y-m-d H:i:s'));
        });

        return redirect('/admin?tab=settings')->with('flash', 'Briefing and media settings updated.');
    }

    public function password(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password:admin'],
            'new_password' => ['required', 'string', 'min:12', 'max:72'],
        ]);
        Auth::guard('admin')->user()->update(['password' => Hash::make($data['new_password'])]);
        $request->session()->regenerate();

        return redirect('/admin?tab=settings')->with('flash', 'Password changed.');
    }
}
