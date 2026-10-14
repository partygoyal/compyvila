<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'year' => ['nullable', 'integer', 'in:2025,2026'],
            'category' => ['nullable', 'string', 'in:'.implode(',', Project::CATEGORIES)],
            'stage' => ['nullable', 'string', 'in:'.implode(',', Project::STAGES)],
        ]);
        $query = Project::published();
        foreach (['year' => 'launch_year', 'category' => 'category', 'stage' => 'stage'] as $filter => $column) {
            if (! empty($filters[$filter])) {
                $query->where($column, $filters[$filter]);
            }
        }
        $q = trim($filters['q'] ?? '');
        if ($q !== '') {
            $query->where(fn ($query) => $query->where('name', 'like', '%'.$q.'%')
                ->orWhere('tagline', 'like', '%'.$q.'%')->orWhere('network', 'like', '%'.$q.'%')
                ->orWhere('symbol', 'like', '%'.$q.'%'));
        }

        return view('projects.index', array_merge($this->shared(), [
            'title' => 'New crypto projects, in perspective.',
            'description' => 'Explore crypto projects with launch milestones in 2025 and 2026. Filter by year, category, and stage, and follow original project sources.',
            'filters' => $filters, 'q' => $q,
            'projects' => $query->orderByDesc('launch_year')->latest('published_at')->orderByDesc('id')->paginate(12)->withQueryString(),
            'yearCounts' => Project::published()->selectRaw('launch_year, COUNT(*) AS total')->groupBy('launch_year')->pluck('total', 'launch_year'),
        ]));
    }

    public function show(string $slug)
    {
        $project = Project::published()->where('slug', $slug)->firstOrFail();

        return view('projects.show', array_merge($this->shared(), [
            'path' => '/projects/'.$project->slug, 'title' => $project->name.' — project overview',
            'description' => $project->tagline, 'project' => $project,
            'related' => Project::published()->where('id', '<>', $project->id)->where('category', $project->category)
                ->latest('published_at')->orderByDesc('id')->take(3)->get(),
        ]));
    }

    private function shared(): array
    {
        return ['path' => '/projects', 'article' => null, 'demo' => setting('demo', '1') === '1'];
    }
}
