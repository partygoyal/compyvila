<article class="project-card" data-category="{{ $project->category }}">
    <div class="project-card-top"><span class="project-monogram" aria-hidden="true">{{ mb_strtoupper(mb_substr($project->name,0,1)) }}</span><span class="project-stage">{{ $project->stage }}</span></div>
    <div class="story-meta"><span class="tag">{{ $project->category }}</span><span>{{ $project->launch_year }} / {{ $project->milestone }}</span></div>
    <h3><a href="{{ route('projects.show', $project->slug) }}">{{ $project->name }} <span>↗</span></a></h3>
    <p>{{ $project->tagline }}</p>
    <div class="project-card-bottom"><span>{{ $project->network ?: 'Network not specified' }}@if($project->symbol) · {{ $project->symbol }}@endif</span><a href="{{ route('projects.show', $project->slug) }}" aria-label="View {{ $project->name }} project">View project →</a></div>
</article>
