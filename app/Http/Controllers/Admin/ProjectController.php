<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function edit(Request $request)
    {
        $project = $request->filled('id') ? Project::findOrFail($request->integer('id')) : new Project;

        return view('admin.project', ['edit' => $project->toArray()]);
    }

    public function store(Request $request)
    {
        $project = $request->integer('id') ? Project::findOrFail($request->integer('id')) : new Project;
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('name', ''))]);
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'slug' => ['required', 'string', 'max:190', Rule::unique('projects', 'slug')->ignore($project->id)],
            'tagline' => ['required', 'string', 'min:10', 'max:300'],
            'description' => ['required', 'string', 'min:30', 'max:30000'],
            'category' => ['required', Rule::in(Project::CATEGORIES)],
            'network' => ['nullable', 'string', 'max:120'],
            'symbol' => ['nullable', 'string', 'max:20'],
            'launch_year' => ['required', 'integer', Rule::in(Project::YEARS)],
            'milestone' => ['required', Rule::in(Project::MILESTONES)],
            'launch_date' => ['nullable', 'date_format:Y-m-d', function ($attribute, $value, $fail) use ($request) {
                if ($value && substr($value, 0, 4) !== (string) $request->input('launch_year')) {
                    $fail('The milestone date must fall within the selected launch year.');
                }
            }],
            'stage' => ['required', Rule::in(Project::STAGES)],
            'website' => ['required', 'url:http,https', 'max:500'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'sources' => ['required_if:status,published', 'nullable', 'string', 'max:10000', function ($attribute, $value, $fail) use ($request) {
                if ($request->input('status') === 'published' && ! trim($value ?? '')) {
                    $fail('Published listings need at least one primary source for the launch milestone.');
                }
                foreach (preg_split('/\R/', $value ?? '') as $source) {
                    if (trim($source) !== '' && ! safe_url(trim($source))) {
                        $fail('Use complete http or https source URLs, one per line.');
                        break;
                    }
                }
            }],
        ]);
        foreach (['network', 'symbol', 'sources'] as $field) {
            $data[$field] = trim($data[$field] ?? '');
        }
        $data['published_at'] = $project->published_at;
        if ($data['status'] === 'published' && ! $data['published_at']) {
            $data['published_at'] = now();
        }
        $project->fill($data)->save();

        return redirect('/admin?tab=projects')->with('flash', 'Project saved. Only published listings appear in the public directory.');
    }
}
