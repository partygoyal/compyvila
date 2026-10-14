@extends('layouts.public')
@section('content')

<div class="breadcrumbs"><a href="/">Home</a><span>/</span><a href="/{{ strtolower(e($article['category'])) }}">{{ $article['category'] }}</a><span>/</span><span>{{ $article['coin'] }}</span></div>
<article class="article-page"><header class="article-heading"><div class="story-meta"><span class="tag">{{ $article['category'] }}</span><span>{{ $article['coin'] }} / {{ human_date($article['published_at']) }}</span></div><h1>{{ $article['title'] }}</h1><p class="article-deck">{{ $article['excerpt'] }}</p><div class="article-tools"><span>{{ max(2,(int)ceil(str_word_count($article['body'])/180)) }} MIN READ <b>•</b> UPDATED {{ human_date($article['updated_at']) }}</span><div><button class="save-button" data-save="{{ $article['slug'] }}" data-title="{{ $article['title'] }}" aria-pressed="false">＋ Save</button><button class="share-button">Share ↗</button></div></div></header><div class="article-art">@if($article['image'])<img src="{{ $article['image'] }}" alt="Chart or illustration for {{ $article['title'] }}">@else
@include('components.art', ['coin' => $article['coin'], 'large' => true])
@endif</div><div class="article-body">{{ paragraphs($article['body']) }}<section class="sources"><span class="eyebrow">GO TO THE SOURCE</span>@foreach(explode("\n",$article['sources']) as $source)
@if(safe_url(trim($source)))<a href="{{ trim($source) }}" target="_blank" rel="noopener noreferrer">{{ parse_url(trim($source),PHP_URL_HOST) }} ↗</a>@endif
@endforeach<p>For educational purposes. Views should be assessed against their evidence and publication date.</p></section></div></article>
<section>@include('components.section-head', ['kicker' => 'KEEP EXPLORING', 'title' => 'There’s more to the story.', 'url' => '/'.strtolower($article['category']), 'label' => 'Explore more'])<div class="card-grid">@foreach($related as $a)
@include('components.card', ['a' => $a])
@endforeach</div></section>

@endsection
