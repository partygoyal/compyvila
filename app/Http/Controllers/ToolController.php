<?php

namespace App\Http\Controllers;

use App\Support\{ToolCatalog, ToolSeo};
use Illuminate\View\View;

class ToolController extends Controller
{
    public function index(): View
    {
        return view('tools.index', array_merge(ToolSeo::data('directory'), ['tools' => ToolCatalog::all()]));
    }

    public function quiz(): View
    {
        return view('quiz', ToolSeo::data('quiz'));
    }

    public function show(string $slug): View
    {
        $tool = ToolCatalog::find($slug);
        abort_unless($tool, 404);
        return view('tools.show', array_merge(ToolSeo::data($slug), ['tool' => $tool, 'tools' => ToolCatalog::all()]));
    }
}
