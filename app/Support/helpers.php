<?php

use App\Models\Setting;
use Illuminate\Support\HtmlString;

function setting(string $name, string $default = ''): string
{
    $request = request();
    if (! $request->attributes->has('publication.settings')) {
        $request->attributes->set('publication.settings', Setting::pluck('value', 'name')->all());
    }

    return $request->attributes->get('publication.settings')[$name] ?? $default;
}

function safe_url(?string $url): bool
{
    return $url && filter_var($url, FILTER_VALIDATE_URL)
        && in_array(strtolower(parse_url($url, PHP_URL_SCHEME) ?? ''), ['http', 'https'], true);
}

function human_date(string $date): string
{
    return date('M j, Y', strtotime($date));
}

function paragraphs(string $body): HtmlString
{
    $html = '';
    foreach (preg_split('/\R\s*\R/', trim($body)) as $paragraph) {
        $html .= str_starts_with($paragraph, '## ')
            ? '<h2>'.e(substr($paragraph, 3)).'</h2>'
            : '<p>'.nl2br(e($paragraph)).'</p>';
    }

    return new HtmlString($html);
}
