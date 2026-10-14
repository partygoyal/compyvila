@if($article)
@php
    $origin = rtrim(config('app.url'), '/');
    $canonical = $origin.'/read/'.$article['slug'];
    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Article', '@id' => $canonical.'#article',
                'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $canonical],
                'headline' => $article['title'], 'description' => $article['excerpt'],
                'datePublished' => \Illuminate\Support\Carbon::parse($article['published_at'])->toAtomString(),
                'dateModified' => \Illuminate\Support\Carbon::parse($article['updated_at'])->toAtomString(),
                'inLanguage' => 'en', 'articleSection' => $article['category'],
                'image' => $article['image'] ? $origin.$article['image'] : $origin.'/assets/compyvila-og.png',
                'publisher' => ['@type' => 'Organization', 'name' => 'Compyvila', 'url' => $origin],
                'citation' => array_values(array_filter(array_map('trim', explode("\n", $article['sources'])), 'safe_url')),
            ],
            [
                '@type' => 'BreadcrumbList', 'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $origin.'/'],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => $article['category'], 'item' => $origin.'/'.strtolower($article['category'])],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => $article['title'], 'item' => $canonical],
                ],
            ],
        ],
    ];
@endphp
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) !!}</script>
@endif
