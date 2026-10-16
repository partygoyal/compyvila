<?php
namespace Tests\Feature;

use App\Models\Article;
use App\Support\SocialPreview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsEditionTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_edition_publishes_once_with_covers_sources_and_original_dates(): void
    {
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-10-15 16:00:00'));
        $this->seed();
        $this->artisan('compyvila:publish-news')->assertExitCode(0);
        $this->assertDatabaseCount('articles',10);
        $this->artisan('compyvila:publish-news',['--publish'=>true])->assertExitCode(0);
        $this->assertDatabaseCount('articles',15);
        $records=json_decode(file_get_contents(base_path('content/news-2026-10-15.json')),true);
        foreach ($records as $record) {
            $article=Article::where('slug',$record['slug'])->firstOrFail();
            $this->assertSame($record['published_at'],$article->published_at);
            $this->get('/read/'.$article->slug)->assertOk()->assertSee('article-banner')->assertSee($record['sources'][0])
                ->assertSee('og:image:width')->assertSee('application/ld+json');
            $this->get('/news')->assertSee($article->title);
            $this->get('/')->assertSee($article->image);
            $this->get('/sitemap.xml')->assertSee('/read/'.$article->slug);
            $preview=SocialPreview::image($article->image);
            $this->assertStringContainsString('/assets/social/article-',$preview['url']);
            $this->assertSame([1200,630],array_slice(getimagesize(public_path(ltrim($article->image,'/'))),0,2));
        }
        $article->update(['status'=>'draft','title'=>'An editorial correction']);
        $this->artisan('compyvila:publish-news',['--publish'=>true])->assertExitCode(0);
        $this->assertDatabaseCount('articles',15);
        $this->assertSame('An editorial correction',$article->fresh()->title);
        $this->assertSame('draft',$article->fresh()->status);
    }
    public function test_second_edition_publishes_beside_first_and_preserves_existing_edits(): void
    {
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-10-16 23:59:00'));
        $this->seed();
        $this->artisan('compyvila:publish-news',['--publish'=>true])->assertExitCode(0);
        $this->artisan('compyvila:publish-news',['edition'=>'../invalid'])->assertExitCode(1);
        $this->artisan('compyvila:publish-news',['edition'=>'2026-10-16'])->assertExitCode(0);
        $this->assertDatabaseCount('articles',15);
        $this->artisan('compyvila:publish-news',['edition'=>'2026-10-16','--publish'=>true])->assertExitCode(0);
        $this->assertDatabaseCount('articles',20);
        $records=json_decode(file_get_contents(base_path('content/news-2026-10-16.json')),true);
        foreach ($records as $record) {
            $article=Article::where('slug',$record['slug'])->firstOrFail();
            $this->get('/read/'.$article->slug)->assertOk()->assertSee('article-banner')->assertSee($record['sources'][0])
                ->assertSee('og:image:width')->assertSee('application/ld+json');
            $this->get('/news')->assertSee($article->title);
            $this->get('/')->assertSee($article->image);
            $this->get('/sitemap.xml')->assertSee('/read/'.$article->slug);
            $this->assertStringContainsString('/assets/social/article-',SocialPreview::image($article->image)['url']);
        }
        $article->update(['status'=>'draft','body'=>'Preserve the editorial correction.']);
        $this->artisan('compyvila:publish-news',['edition'=>'2026-10-16','--publish'=>true])->assertExitCode(0);
        $this->assertDatabaseCount('articles',20);
        $this->assertSame('draft',$article->fresh()->status);
        $this->assertSame('Preserve the editorial correction.',$article->fresh()->body);
    }

}
