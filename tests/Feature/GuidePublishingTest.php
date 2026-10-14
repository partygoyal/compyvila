<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuidePublishingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guides_publish_once_and_existing_editorial_changes_survive(): void
    {
        $this->seed();
        $this->artisan('compyvila:publish-guides')->assertExitCode(0);
        $this->assertDatabaseCount('articles', 10);
        $this->artisan('compyvila:publish-guides', ['--publish' => true])->assertExitCode(0);
        $this->assertDatabaseCount('articles', 16);
        $guide = Article::where('slug', 'seed-phrase-vs-private-key')->firstOrFail();
        $guide->update(['body' => 'An editor has revised this article.', 'status' => 'draft']);
        $this->artisan('compyvila:publish-guides', ['--publish' => true])->assertExitCode(0);
        $this->assertDatabaseCount('articles', 16);
        $this->assertSame('An editor has revised this article.', $guide->fresh()->body);
        $this->assertSame('draft', $guide->fresh()->status);
    }

    public function test_published_guides_have_rendered_citations_valid_schema_and_sitemap_dates(): void
    {
        $this->seed();
        config(['app.url' => 'https://compyvila.com']);
        $this->artisan('compyvila:publish-guides', ['--publish' => true])->assertExitCode(0);
        $guides = json_decode(file_get_contents(base_path('content/guides.json')), true, flags: JSON_THROW_ON_ERROR);
        foreach ($guides as $guide) {
            $html = $this->get('/read/'.$guide['slug'])->assertOk()
                ->assertSee('<h2>Frequently asked questions</h2>', false)
                ->assertSee('application/ld+json', false)->getContent();
            preg_match('~<script type="application/ld\+json">(.*?)</script>~s', $html, $match);
            $schema = json_decode($match[1], true, flags: JSON_THROW_ON_ERROR);
            $this->assertSame($guide['title'], $schema['@graph'][0]['headline']);
            $this->assertSame($guide['sources'], $schema['@graph'][0]['citation']);
            $this->assertArrayNotHasKey('author', $schema['@graph'][0]);
            $this->assertSame('BreadcrumbList', $schema['@graph'][1]['@type']);
            preg_match_all('~href="(/read/[^"#]+)"~', $html, $links);
            foreach (array_unique($links[1]) as $link) {
                $this->get($link)->assertOk();
            }
        }
        $this->get('/read/market-cap-vs-fdv')->assertSee('<table>', false)->assertDontSee('| --- |');
        $sitemap = $this->get('/sitemap.xml')->assertOk()->assertSee('<lastmod>', false)
            ->assertDontSee('https://compyvila.com/search')->assertDontSee('https://compyvila.com/watchlist');
        foreach ($guides as $guide) {
            $sitemap->assertSee('/read/'.$guide['slug']);
        }
        $this->get('/robots.txt')->assertOk()->assertSee('Sitemap: https://compyvila.com/sitemap.xml');
    }

    public function test_markdown_escapes_html_and_disallows_script_links(): void
    {
        $html = (string) paragraphs("## Heading\n\n<script>alert(1)</script>\n\n[unsafe](javascript:alert%281%29)\n\n[Source](https://bitcoin.org/)\n\n- Item");
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('href="javascript:', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringContainsString('href="https://bitcoin.org/"', $html);
        $this->assertStringContainsString('<li>Item</li>', $html);
    }
}
