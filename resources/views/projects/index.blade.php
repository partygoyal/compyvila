@extends('layouts.public')
@section('content')
<section class="project-intro">
    <div><span class="eyebrow">THE COMPYVILA PROJECT DIRECTORY / 2025–2026</span><h1>Discover what’s<br>being built<span>.</span></h1><p>New networks, protocols, and products. Explore the projects, understand their launch milestones, and go straight to the source.</p><a class="text-link" href="#project-listings">Explore the directory ↓</a></div>
    <div class="project-year-art" aria-hidden="true"><span>THE NEXT CHAPTER</span><strong>2025<span>↗</span>2026</strong><small>IDEAS. NETWORKS. NEW POSSIBILITIES.</small></div>
</section>
<div class="project-directory-note"><span class="eyebrow">CONTEXT BEFORE HYPE</span><p>Years refer to a specific launch milestone, identified on each listing. A listing is an editorial reference, not an endorsement. Project details are maintained manually.</p></div>
<section id="project-listings" class="project-listings" aria-labelledby="projects-heading">
    <div class="project-list-heading"><h2 id="projects-heading">Find your next research question.</h2><div class="year-tabs" aria-label="Filter launch year">
        <a href="{{ route('projects.index', array_filter(array_merge($filters, ['year'=>null]))) }}" @class(['selected'=>empty($filters['year'])]) @if(empty($filters['year'])) aria-current="true" @endif>All years <b>{{ $yearCounts->sum() }}</b></a>
        @foreach(\App\Models\Project::YEARS as $year)<a href="{{ route('projects.index', array_filter(array_merge($filters, ['year'=>$year]))) }}" @class(['selected'=>(string)($filters['year'] ?? '')===(string)$year]) @if((string)($filters['year'] ?? '')===(string)$year) aria-current="true" @endif>{{ $year }} <b>{{ $yearCounts[$year] ?? 0 }}</b></a>@endforeach
    </div></div>
    <form class="project-filters" method="get" action="/projects">
        <label>SEARCH PROJECTS<input type="search" name="q" maxlength="120" value="{{ $q }}" placeholder="Name, ticker, or network"></label>
        <label>LAUNCH YEAR<select name="year"><option value="">All years</option>@foreach(\App\Models\Project::YEARS as $year)<option value="{{ $year }}" @selected((string)($filters['year'] ?? '')===(string)$year)>{{ $year }}</option>@endforeach</select></label>
        <label>CATEGORY<select name="category"><option value="">All categories</option>@foreach(\App\Models\Project::CATEGORIES as $category)<option @selected(($filters['category'] ?? '')===$category)>{{ $category }}</option>@endforeach</select></label>
        <label>CURRENT STAGE<select name="stage"><option value="">All stages</option>@foreach(\App\Models\Project::STAGES as $stage)<option @selected(($filters['stage'] ?? '')===$stage)>{{ $stage }}</option>@endforeach</select></label>
        <button class="button dark">Apply filters ↗</button>
    </form>
    <div class="listing-label"><span class="eyebrow">{{ $projects->total() }} {{ $projects->total()===1 ? 'PROJECT' : 'PROJECTS' }} TO EXPLORE</span><a class="text-link" href="/projects">Reset filters</a></div>
    @if($projects->count())
    <div class="project-grid">@foreach($projects as $project)@include('projects.card')@endforeach</div>
    <nav class="pagination" aria-label="Projects pagination">
        @if($projects->previousPageUrl())<a href="{{ $projects->previousPageUrl() }}" aria-label="Previous page">←</a>@endif
        @foreach($projects->getUrlRange(max(1,$projects->currentPage()-2), min($projects->lastPage(),$projects->currentPage()+2)) as $page=>$url)<a href="{{ $url }}" @class(['selected'=>$page===$projects->currentPage()]) @if($page===$projects->currentPage()) aria-current="page" @endif aria-label="Page {{ $page }}">{{ $page }}</a>@endforeach
        @if($projects->nextPageUrl())<a href="{{ $projects->nextPageUrl() }}" aria-label="Next page">→</a>@endif
    </nav>
    @else
    <div class="empty-state project-empty"><span>↗</span><h2>{{ $yearCounts->sum() ? 'No projects match those filters.' : 'The directory starts here.' }}</h2><p>{{ $yearCounts->sum() ? 'Try another year, category, or search term.' : 'We’re preparing our 2025 and 2026 project listings. Published profiles will appear here with launch milestones and original sources.' }}</p>@if($yearCounts->sum())<a class="button" href="/projects">Clear filters ↗</a>@else<a class="button" href="/contact">Suggest a project ↗</a>@endif</div>
    @endif
</section>
<section class="project-method"><div><span class="eyebrow">HOW TO READ A LISTING</span><h2>A starting point.<br>A clearer picture.</h2></div><div><h3>Know the milestone.</h3><p>The listed year belongs to the named announcement or launch. It does not imply every part of the project launched in that year.</p><h3>Check the evidence.</h3><p>Follow the project website and source links. Review dates indicate when the listing was last edited; plans and project conditions can change.</p><h3>Keep researching.</h3><p>Compare claims, documentation, and risks. Start with our <a class="text-link" href="/learn">practical crypto guides ↗</a>.</p></div></section>
@endsection
