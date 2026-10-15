@extends('layouts.public')
@section('content')
<div class="project-directory">
<section class="project-intro">
    <div><span class="eyebrow"><i></i> THE COMPYVILA CRYPTO RADAR</span><h1>Crypto <em>Radar.</em></h1><p>Explore new projects through their launch milestones, what they’re building, and the sources behind their claims.</p></div>
    <div class="project-edition"><span>THE LAUNCH INDEX</span><strong>2025<span>—</span>2026</strong><small>Networks · Protocols · Products</small><a href="/contact">Suggest a project <span>↗</span></a></div>
</section>
<section id="project-listings" class="project-listings" aria-labelledby="projects-heading">
    <div class="project-list-heading"><div><span class="eyebrow">DISCOVER / RESEARCH / EXPLORE</span><h2 id="projects-heading">On the radar<span>.</span></h2></div><div class="year-tabs" aria-label="Filter launch year">
        <a href="{{ route('projects.index', array_filter(array_merge($filters, ['year'=>null]))) }}" @class(['selected'=>empty($filters['year'])]) @if(empty($filters['year'])) aria-current="true" @endif>All years <b>{{ $yearCounts->sum() }}</b></a>
        @foreach(\App\Models\Project::YEARS as $year)<a href="{{ route('projects.index', array_filter(array_merge($filters, ['year'=>$year]))) }}" @class(['selected'=>(string)($filters['year'] ?? '')===(string)$year]) @if((string)($filters['year'] ?? '')===(string)$year) aria-current="true" @endif>{{ $year }} <b>{{ $yearCounts[$year] ?? 0 }}</b></a>@endforeach
    </div></div>
    <div class="project-directory-layout"><aside class="project-filter-panel" aria-label="Project filters"><div class="project-filter-title"><h3>Refine your search</h3><a href="/projects">Reset ↺</a></div><form class="project-filters" method="get" action="/projects">
        <label>SEARCH PROJECTS<input type="search" name="q" maxlength="120" value="{{ $q }}" placeholder="Name, ticker, or network"></label>
        <label>LAUNCH YEAR<select name="year"><option value="">All years</option>@foreach(\App\Models\Project::YEARS as $year)<option value="{{ $year }}" @selected((string)($filters['year'] ?? '')===(string)$year)>{{ $year }}</option>@endforeach</select></label>
        <label>CATEGORY<select name="category"><option value="">All categories</option>@foreach(\App\Models\Project::CATEGORIES as $category)<option @selected(($filters['category'] ?? '')===$category)>{{ $category }}</option>@endforeach</select></label>
        <label>CURRENT STAGE<select name="stage"><option value="">All stages</option>@foreach(\App\Models\Project::STAGES as $stage)<option @selected(($filters['stage'] ?? '')===$stage)>{{ $stage }}</option>@endforeach</select></label>
        <button class="button dark">Apply filters ↗</button>
    </form><div class="project-filter-note"><span>↗</span><p>A year identifies a launch milestone. Each profile explains which one.</p></div></aside><div class="project-results">
    <div class="listing-label"><span class="eyebrow">{{ $projects->total() }} {{ $projects->total()===1 ? 'PROJECT' : 'PROJECTS' }} TO EXPLORE</span><span class="project-sort">2026 → 2025</span></div>
    @if($projects->count())
    <div class="project-grid">@foreach($projects as $project)@include('projects.card')@endforeach</div>
    <nav class="pagination" aria-label="Projects pagination">
        @if($projects->previousPageUrl())<a href="{{ $projects->previousPageUrl() }}" aria-label="Previous page">←</a>@endif
        @foreach($projects->getUrlRange(max(1,$projects->currentPage()-2), min($projects->lastPage(),$projects->currentPage()+2)) as $page=>$url)<a href="{{ $url }}" @class(['selected'=>$page===$projects->currentPage()]) @if($page===$projects->currentPage()) aria-current="page" @endif aria-label="Page {{ $page }}">{{ $page }}</a>@endforeach
        @if($projects->nextPageUrl())<a href="{{ $projects->nextPageUrl() }}" aria-label="Next page">→</a>@endif
    </nav>
    @else
    <div class="empty-state project-empty"><div class="project-empty-art" aria-hidden="true"><svg viewBox="0 0 180 150" fill="none"><ellipse cx="90" cy="75" rx="77" ry="36" transform="rotate(-25 90 75)"/><ellipse cx="90" cy="75" rx="77" ry="36" transform="rotate(35 90 75)"/><circle cx="90" cy="75" r="34"/><path d="M76 88 104 60m-22 0h22v22"/><circle class="orbit-dot" cx="155" cy="37" r="6"/></svg></div><span class="eyebrow">{{ $yearCounts->sum() ? 'A DIFFERENT VIEW' : 'THE INDEX IS TAKING SHAPE' }}</span><h2>{{ $yearCounts->sum() ? 'No projects match those filters.' : 'The directory starts here.' }}</h2><p>{{ $yearCounts->sum() ? 'Try another year, category, or search term.' : 'Our first 2025–2026 profiles are on the way. Know a project we should cover? Share its official website and launch announcement.' }}</p>@if($yearCounts->sum())<a class="button" href="/projects">Clear filters ↗</a>@else<a class="button" href="/contact">Suggest a project ↗</a>@endif</div>
    @endif
</div></div></section>
<section class="project-method" aria-label="Our listing approach"><div><span class="project-method-number">01</span><h3>Milestones, made clear.</h3><p>Announcement, testnet, mainnet, or token launch. Every year has context.</p></div><div><span class="project-method-number">02</span><h3>Follow the original source.</h3><p>Project websites, documentation, and launch announcements, in one place.</p></div><div><span class="project-method-number">03</span><h3>Research comes first.</h3><p>A listing is a starting point, not an endorsement. <a href="/learn">Build your knowledge ↗</a></p></div></section>
</div>
@endsection
