<?php

namespace App\Http\Controllers;

use App\Support\ToolCatalog;
use Illuminate\View\View;

class ToolController extends Controller
{
    public function index(): View
    {
        return view('tools.index', ['title' => 'Free Crypto Tools & Calculators', 'description' => 'Twelve free browser-based crypto calculators, converters and planning tools. Manual inputs, no API or account required.', 'tools' => ToolCatalog::all()]);
    }

    public function show(string $slug): View
    {
        $tool = ToolCatalog::find($slug);
        abort_unless($tool, 404);
        return view('tools.show', ['title' => $tool['title'], 'description' => $tool['summary'], 'tool' => $tool, 'tools' => ToolCatalog::all()]);
    }
}
