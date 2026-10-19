<?php

namespace App\Http\Controllers;

use App\Support\ToolCatalog;
use Illuminate\View\View;

class ToolController extends Controller
{
    public function index(): View
    {
        return view('tools.index', ['title' => 'Free Crypto Tools & Calculators', 'description' => 'Eleven free browser-based crypto calculators, converters and planning tools. Manual inputs, no API or account required.', 'tools' => ToolCatalog::all()]);
    }

    public function quiz(): View
    {
        return view('quiz', ['title' => 'Crypto Knowledge Quiz', 'description' => 'Test your crypto knowledge with ten free interactive questions about Bitcoin, wallets, Ethereum and token fundamentals. Get an explanation after every answer.']);
    }

    public function show(string $slug): View
    {
        $tool = ToolCatalog::find($slug);
        abort_unless($tool, 404);
        return view('tools.show', ['title' => $tool['title'], 'description' => $tool['summary'], 'tool' => $tool, 'tools' => ToolCatalog::all()]);
    }
}
