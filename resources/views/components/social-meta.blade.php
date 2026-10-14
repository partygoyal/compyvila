@php
    $origin = rtrim(config('app.url'), '/');
    $canonical = $origin.($path === '/' ? '/' : $path);
    $shareImage = $origin.'/assets/compyvila-og.png';
    $shareTitle = $title.' — Compyvila';
    $imageAlt = 'Compyvila — Crypto, with perspective. Markets, analysis, and updates, with an orange glass Bitcoin lens.';
@endphp
<link rel="canonical" href="{{ $canonical }}">
<meta property="og:site_name" content="Compyvila">
<meta property="og:locale" content="en_IN">
<meta property="og:type" content="{{ $article ? 'article' : 'website' }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:title" content="{{ $shareTitle }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $shareImage }}">
<meta property="og:image:secure_url" content="{{ $shareImage }}">
<meta property="og:image:type" content="image/png">
<meta property="og:image:width" content="1730">
<meta property="og:image:height" content="909">
<meta property="og:image:alt" content="{{ $imageAlt }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $shareTitle }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $shareImage }}">
<meta name="twitter:image:alt" content="{{ $imageAlt }}">
@if($article)
<meta property="article:published_time" content="{{ \Illuminate\Support\Carbon::parse($article->published_at)->toIso8601String() }}">
<meta property="article:modified_time" content="{{ \Illuminate\Support\Carbon::parse($article->updated_at)->toIso8601String() }}">
@endif
