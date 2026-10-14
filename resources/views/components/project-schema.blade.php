@if(request()->routeIs('projects.show'))
@php
    $origin = rtrim(config('app.url'), '/');
    $url = $origin.'/projects/'.$project->slug;
    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'WebPage', '@id' => $url, 'url' => $url,
                'name' => $project->name.' — project overview', 'description' => $project->tagline,
                'datePublished' => $project->published_at->toAtomString(), 'dateModified' => $project->updated_at->toAtomString(),
                'inLanguage' => 'en', 'about' => ['@type'=>'Thing', 'name'=>$project->name, 'url'=>$project->website],
                'publisher' => ['@type'=>'Organization', 'name'=>'Compyvila', 'url'=>$origin],
                'citation' => array_values(array_filter(array_map('trim', preg_split('/\R/', $project->sources)), 'safe_url')),
            ],
            [
                '@type'=>'BreadcrumbList', 'itemListElement'=>[
                    ['@type'=>'ListItem', 'position'=>1, 'name'=>'Home', 'item'=>$origin.'/'],
                    ['@type'=>'ListItem', 'position'=>2, 'name'=>'Projects', 'item'=>$origin.'/projects'],
                    ['@type'=>'ListItem', 'position'=>3, 'name'=>$project->name, 'item'=>$url],
                ],
            ],
        ],
    ];
@endphp
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) !!}</script>
@endif
