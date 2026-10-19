<?php

namespace Tests\Feature;

use App\Support\{ToolCatalog, ToolSeo};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToolSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_page_has_matching_crawlable_content_metadata_and_structured_data(): void
    {
        $this->seed();
        config(['app.url' => 'https://compyvila.com']);
        $titles = $descriptions = [];
        foreach (array_merge(['directory', 'quiz'], array_column(ToolCatalog::all(), 'slug')) as $key) {
            $path = $key === 'directory' ? '/tools' : ($key === 'quiz' ? '/quiz' : '/tools/'.$key);
            $seo = ToolSeo::content($key);
            $response = $this->get($path)->assertOk()->assertSee($seo['answer'])->assertSee($seo['title'])
                ->assertSee('<meta name="description" content="'.e($seo['description']).'">', false)
                ->assertSee('<link rel="canonical" href="https://compyvila.com'.$path.'">', false)
                ->assertSee('index, follow, max-image-preview:large');
            $html = $response->getContent();
            $this->assertSame(1, substr_count($html, '<h1>'));
            preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $match);
            $schema = json_decode($match[1], true, flags: JSON_THROW_ON_ERROR);
            $graph = collect($schema['@graph'])->keyBy('@type');
            $expected = $key === 'directory' ? 'CollectionPage' : 'WebPage';
            $this->assertSame('https://compyvila.com'.$path, $graph[$expected]['url']);
            $this->assertSame(ToolSeo::UPDATED_AT, $graph[$expected]['dateModified']);
            if ($key === 'directory') $this->assertSame(11, $graph['ItemList']['numberOfItems']);
            elseif ($key === 'quiz') $this->assertSame('Crypto Quiz', $graph['Quiz']['name']);
            else {
                $this->assertTrue($graph['WebApplication']['isAccessibleForFree']);
                $this->assertSame('0', $graph['WebApplication']['offers']['price']);
                $this->assertArrayNotHasKey('aggregateRating', $graph['WebApplication']);
            }
            foreach ($graph['FAQPage']['mainEntity'] as $i => $question) {
                $this->assertSame($seo['faq'][$i]['question'], $question['name']);
                $this->assertSame($seo['faq'][$i]['answer'], $question['acceptedAnswer']['text']);
                $response->assertSee($question['name'])->assertSee($question['acceptedAnswer']['text']);
            }
            $titles[] = $seo['title']; $descriptions[] = $seo['description'];
        }
        $this->assertCount(13, array_unique($titles));
        $this->assertCount(13, array_unique($descriptions));
        $this->get('/sitemap.xml')->assertSee('<lastmod>'.ToolSeo::UPDATED_AT.'</lastmod>', false);
        $robots = $this->get('/robots.txt')->assertOk();
        $robots->assertSee('Allow: /')->assertDontSee('Disallow: /tools')->assertDontSee('Disallow: /quiz');
        $this->get('/')->assertSee('>Crypto Quiz</a>', false)->assertDontSee('>Quiz</a>', false);
    }

    public function test_relevant_articles_link_back_to_the_tools_and_quiz(): void
    {
        $this->seed();
        $this->artisan('compyvila:publish-guides', ['--publish' => true])->assertExitCode(0);
        $this->get('/read/market-cap-vs-fdv')->assertOk()->assertSee('href="/tools/market-cap-fdv"', false)->assertSee('PUT IT INTO PRACTICE');
        $this->get('/read/seed-phrase-vs-private-key')->assertOk()->assertSee('href="/quiz"', false)->assertSee('PUT IT INTO PRACTICE');
    }
}
