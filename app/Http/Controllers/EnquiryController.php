<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'company' => ['nullable', 'string', 'max:190'],
            'placement' => ['required', 'string', 'max:100'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'website' => ['nullable', 'max:0'],
        ]);
        unset($data['website']);
        $data['company'] ??= '';
        $data['created_at'] = now()->format('Y-m-d H:i:s');
        Enquiry::create($data);

        return redirect('/media-kit#enquire')->with('flash', 'Your media enquiry has been saved. Thank you for your interest in Compyvila.');
    }
}
