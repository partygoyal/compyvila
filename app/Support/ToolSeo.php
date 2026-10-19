<?php

namespace App\Support;

final class ToolSeo
{
    public const UPDATED_AT = '2026-10-19';

    public static function content(string $key): array
    {
        $pages = json_decode(file_get_contents(resource_path('data/tool-seo.json')), true, flags: JSON_THROW_ON_ERROR);
        return $pages[$key];
    }

    public static function relatedForArticle(string $slug): array
    {
        $pages = json_decode(file_get_contents(resource_path('data/tool-seo.json')), true, flags: JSON_THROW_ON_ERROR);
        $related = [];
        foreach ($pages as $key => $page) {
            foreach ($page['links'] as $link) {
                if ($link['url'] === '/read/'.$slug) {
                    $related[] = ['title' => $page['title'], 'url' => $key === 'quiz' ? '/quiz' : '/tools/'.$key];
                    break;
                }
            }
        }
        return array_slice($related, 0, 3);
    }

    public static function data(string $key): array
    {
        $seo = self::content($key);
        $origin = rtrim(config('app.url'), '/');
        $path = $key === 'directory' ? '/tools' : ($key === 'quiz' ? '/quiz' : '/tools/'.$key);
        $url = $origin.$path;
        $social = json_decode(file_get_contents(resource_path('data/tool-social.json')), true, flags: JSON_THROW_ON_ERROR)[$key];
        $publisher = ['@type' => 'Organization', '@id' => $origin.'/#organization', 'name' => 'Compyvila', 'url' => $origin];
        $crumbs = [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $origin.'/'],
            ['@type' => 'ListItem', 'position' => 2, 'name' => $key === 'quiz' ? 'Crypto Quiz' : 'Free Crypto Tools', 'item' => $key === 'quiz' ? $url : $origin.'/tools'],
        ];
        if (!in_array($key, ['directory', 'quiz'])) {
            $crumbs[] = ['@type' => 'ListItem', 'position' => 3, 'name' => $seo['title'], 'item' => $url];
        }
        $page = [
            '@type' => $key === 'directory' ? 'CollectionPage' : 'WebPage', '@id' => $url,
            'url' => $url, 'name' => $seo['title'], 'description' => $seo['description'],
            'inLanguage' => 'en', 'isAccessibleForFree' => true, 'dateModified' => self::UPDATED_AT,
            'image' => $origin.$social['path'],
            'publisher' => $publisher, 'breadcrumb' => ['@id' => $url.'#breadcrumbs'],
            'hasPart' => ['@id' => $url.'#faq'], 'mainEntity' => ['@id' => $url.'#resource'],
        ];
        if ($key === 'directory') {
            $resource = ['@type' => 'ItemList', '@id' => $url.'#resource', 'name' => 'Free Crypto Tools', 'numberOfItems' => count(ToolCatalog::all()), 'itemListElement' => []];
            foreach (ToolCatalog::all() as $i => $tool) {
                $resource['itemListElement'][] = ['@type' => 'ListItem', 'position' => $i+1, 'name' => $tool['title'], 'url' => $origin.'/tools/'.$tool['slug']];
            }
        } elseif ($key === 'quiz') {
            $resource = ['@type' => 'Quiz', '@id' => $url.'#resource', 'url' => $url, 'name' => 'Crypto Quiz', 'description' => $seo['answer'], 'isAccessibleForFree' => true, 'inLanguage' => 'en', 'learningResourceType' => 'Quiz', 'educationalUse' => 'Self-assessment', 'publisher' => $publisher];
        } else {
            $resource = ['@type' => 'WebApplication', '@id' => $url.'#resource', 'url' => $url, 'name' => $seo['title'], 'description' => $seo['answer'], 'applicationCategory' => 'FinanceApplication', 'operatingSystem' => 'Any', 'browserRequirements' => 'Requires JavaScript and a modern web browser.', 'isAccessibleForFree' => true, 'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'USD'], 'publisher' => $publisher];
        }
        $faq = ['@type' => 'FAQPage', '@id' => $url.'#faq', 'mainEntity' => array_map(fn ($item) => ['@type' => 'Question', 'name' => $item['question'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['answer']]], $seo['faq'])];
        return ['socialPreview' => ['url' => $origin.$social['path'], 'type' => 'image/jpeg', 'width' => $social['width'], 'height' => $social['height']], 'socialImageAlt' => $social['alt'], 'seo' => $seo, 'title' => $seo['title'], 'description' => $seo['description'], 'pageSchema' => ['@context' => 'https://schema.org', '@graph' => [$publisher, $page, $resource, ['@type' => 'BreadcrumbList', '@id' => $url.'#breadcrumbs', 'itemListElement' => $crumbs], $faq]]];
    }
}
