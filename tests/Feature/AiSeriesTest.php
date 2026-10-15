<?php

namespace Tests\Feature;

use App\Models\{Admin, Article};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AiSeriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_series_publishes_complete_bannered_articles_and_preserves_editorial_changes(): void
    {
        $this->seed();
        $this->artisan('compyvila:publish-ai-series')->assertExitCode(0);
        $this->assertDatabaseCount('articles', 10);
        $this->artisan('compyvila:publish-ai-series', ['--publish'=>true])->assertExitCode(0);
        $this->assertDatabaseCount('articles', 13);
        $article = Article::where('slug','ai-agents-crypto-wallets')->firstOrFail();
        $article->update(['body'=>'A later editorial correction.', 'status'=>'draft']);
        $this->artisan('compyvila:publish-ai-series', ['--publish'=>true])->assertExitCode(0);
        $this->assertDatabaseCount('articles', 13);
        $this->assertSame('A later editorial correction.', $article->fresh()->body);
        $this->assertSame('draft', $article->fresh()->status);
    }

    public function test_banners_are_used_for_article_hero_social_previews_and_schema(): void
    {
        $this->seed();
        config(['app.url'=>'https://compyvila.com']);
        $this->artisan('compyvila:publish-guides', ['--publish'=>true])->assertExitCode(0);
        $this->artisan('compyvila:publish-projects', ['--publish'=>true])->assertExitCode(0);
        $this->artisan('compyvila:publish-ai-series', ['--publish'=>true])->assertExitCode(0);
        $manifest = json_decode(file_get_contents(base_path('content/ai-series.json')), true, flags: JSON_THROW_ON_ERROR);
        foreach ($manifest as $article) {
            $image = public_path(ltrim($article['image'], '/'));
            [$width,$height,$type] = getimagesize($image);
            $this->assertSame(IMAGETYPE_WEBP, $type);
            $this->assertGreaterThan($height,$width);
            $this->assertLessThan(1024*1024,filesize($image));
            $html = $this->get('/read/'.$article['slug'])->assertOk()
                ->assertSee('article-banner')->assertSee('Frequently asked questions')
                ->assertSee('<meta property="og:image" content="https://compyvila.com'.$article['image'].'">', false)
                ->assertSee('<meta property="og:image:type" content="image/webp">', false)->getContent();
            preg_match('~<script type="application/ld\+json">(.*?)</script>~s',$html,$schema);
            $data = json_decode($schema[1],true,flags:JSON_THROW_ON_ERROR);
            $this->assertSame('https://compyvila.com'.$article['image'],$data['@graph'][0]['image']);
            preg_match_all('~href="(/(?:read|projects)/[^"#]+)"~',$html,$links);
            foreach (array_unique($links[1]) as $link) {
                $this->get($link)->assertOk();
            }
            $this->get('/sitemap.xml')->assertSee('/read/'.$article['slug']);
        }
        $this->get('/')->assertOk()->assertSee('/assets/articles/akash-ai-gpu-compute.webp');
    }

    public function test_deleting_a_bundled_banner_article_cannot_remove_an_unrelated_upload(): void
    {
        $this->seed();
        Storage::fake('editorial');
        Storage::disk('editorial')->put('akash-ai-gpu-compute.webp','Unrelated uploaded file with matching basename.');
        $this->artisan('compyvila:publish-ai-series', ['--publish'=>true])->assertExitCode(0);
        $article = Article::where('slug','akash-ai-gpu-compute')->firstOrFail();
        $this->actingAs(Admin::create(['email'=>'ai-editor@example.com','password'=>'safe-test-password']), 'admin');
        $this->delete('/admin/records/articles/'.$article->id)->assertRedirect();
        Storage::disk('editorial')->assertExists('akash-ai-gpu-compute.webp');
        $this->assertFileExists(public_path('assets/articles/akash-ai-gpu-compute.webp'));
    }
}
