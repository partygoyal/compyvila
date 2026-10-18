<?php

namespace Tests\Feature;

use App\Models\{Admin, Article, Enquiry, Event, Setting, Snapshot, Subscriber};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{DB, Hash, Storage};
use Tests\TestCase;

class PublicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function admin(): Admin
    {
        return Admin::create(['email' => 'editor@example.com', 'password' => 'a-safe-test-password']);
    }

    private function story(array $overrides = []): array
    {
        return array_merge([
            'title' => 'An evidence-led research story', 'slug' => 'evidence-led-story',
            'excerpt' => 'A practical research note with primary sources.',
            'body' => "## A useful heading\n\nResearch starts with evidence and gives readers a clear way to verify it.",
            'category' => 'Analysis', 'coin' => 'Bitcoin', 'sources' => 'https://bitcoin.org/',
            'status' => 'published', 'published_at' => now()->subDay()->format('Y-m-d\TH:i'),
        ], $overrides);
    }

    public function test_public_sections_render_without_authors_or_about_pages(): void
    {
        foreach (['/', '/news', '/analysis', '/bitcoin', '/ethereum', '/altcoins', '/learn', '/calendar', '/media-kit', '/market-notes', '/search', '/watchlist', '/contact', '/privacy', '/terms', '/editorial-policy'] as $url) {
            $this->get($url)->assertOk()->assertSee('Compyvila')->assertDontSee('href="/about"', false);
        }
        $this->get('/about')->assertNotFound();
        $this->get('/.env')->assertNotFound();
        $this->get('/storage/admin-access.txt')->assertNotFound();
    }

    public function test_home_hero_uses_only_the_latest_ten_published_stories(): void
    {
        $expected = [];
        for ($i = 1; $i <= 12; $i++) {
            $title = 'Latest blog number '.$i;
            Article::create(array_merge($this->story(), [
                'slug' => 'latest-blog-'.$i, 'title' => $title,
                'published_at' => now()->subMinutes($i)->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s'), 'image' => '', 'featured' => false,
            ]));
            if ($i <= 10) $expected[] = $title;
        }
        Article::create(array_merge($this->story(), ['slug' => 'hidden-draft', 'title' => 'Hidden draft', 'status' => 'draft', 'image' => '', 'featured' => true, 'updated_at' => now()->format('Y-m-d H:i:s')]));
        Article::create(array_merge($this->story(), ['slug' => 'future-story', 'title' => 'Future story', 'published_at' => now()->addDay()->format('Y-m-d H:i:s'), 'image' => '', 'featured' => true, 'updated_at' => now()->format('Y-m-d H:i:s')]));
        $response = $this->get('/')->assertOk()->assertViewHas('heroArticles', fn ($posts) => $posts->pluck('title')->all() === $expected);
        $this->assertSame(10, substr_count($response->getContent(), 'data-hero-slide role='));
        $response->assertDontSee('Hidden draft')->assertDontSee('Future story');
    }

    public function test_hero_handles_one_post_and_an_empty_publication(): void
    {
        Article::where('id', '<>', Article::first()->id)->delete();
        $this->get('/')->assertOk()->assertViewHas('heroArticles', fn ($posts) => $posts->count() === 1)->assertDontSee('data-hero-next', false);
        Article::query()->delete();
        $this->get('/')->assertOk()->assertSee('Fresh stories are on their way.')->assertDontSee('data-hero-next', false);
    }

    public function test_the_existing_schema_and_seeding_are_adopted_without_overwriting_records(): void
    {
        $admin = $this->admin();
        Setting::put('brief_title', 'An edited briefing that must survive.');
        Article::first()->update(['title' => 'An edited story that must survive.']);
        $migration = require database_path('migrations/2026_10_14_000000_create_publication_tables.php');
        $migration->up();
        $this->seed();
        $this->assertDatabaseCount('articles', 10);
        $this->assertDatabaseCount('admins', 1);
        $this->assertSame($admin->password, $admin->fresh()->password);
        $this->assertDatabaseHas('settings', ['name' => 'brief_title', 'value' => 'An edited briefing that must survive.']);
        $this->assertDatabaseHas('articles', ['title' => 'An edited story that must survive.']);
    }

    public function test_existing_plain_php_password_hashes_authenticate_with_laravel(): void
    {
        DB::table('admins')->insert(['email' => 'existing@example.com', 'password' => password_hash('existing-test-password', PASSWORD_DEFAULT)]);
        $this->post('/admin/login', ['email' => 'existing@example.com', 'password' => 'existing-test-password'])->assertRedirect('/admin');
        $this->assertAuthenticated('admin');
        $this->get('/admin')->assertOk()->assertSee('Welcome to the desk.');
    }

    public function test_admin_data_and_writes_require_authentication(): void
    {
        foreach (['/admin', '/admin/article', '/admin/event', '/admin/snapshot', '/admin/export'] as $url) {
            $this->get($url)->assertRedirect('/admin/login');
        }
        $this->post('/admin/article', $this->story())->assertRedirect('/admin/login');
        $this->delete('/admin/records/articles/1')->assertRedirect('/admin/login');
        $this->assertDatabaseCount('articles', 10);
    }

    public function test_all_editorial_tabs_and_forms_render(): void
    {
        $this->actingAs($this->admin(), 'admin');
        foreach (['overview', 'articles', 'market', 'events', 'subscribers', 'enquiries', 'settings'] as $tab) {
            $this->get('/admin?tab='.$tab)->assertOk();
        }
        foreach (['/admin/article', '/admin/article?id=1', '/admin/event', '/admin/event?id=1', '/admin/snapshot', '/admin/snapshot?id=1'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_publishing_drafts_and_future_stories_uses_eloquent_visibility(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $this->post('/admin/article', $this->story(['status' => 'draft']))->assertRedirect('/admin?tab=articles');
        $article = Article::where('slug', 'evidence-led-story')->firstOrFail();
        $this->get('/read/'.$article->slug)->assertNotFound();
        $this->post('/admin/article', $this->story(['id' => $article->id, 'published_at' => now()->addDay()->format('Y-m-d\TH:i')]))->assertRedirect();
        $this->get('/read/'.$article->slug)->assertNotFound();
        $this->travel(2)->days();
        $this->get('/read/'.$article->slug)->assertOk()->assertSee('<h2>A useful heading</h2>', false);
        $this->travelBack();
    }

    public function test_validation_preserves_article_data_and_escapes_body_html(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $this->post('/admin/article', $this->story(['sources' => 'javascript:alert(1)']))->assertSessionHasErrors('sources');
        $this->post('/admin/article', $this->story(['sources' => '']))->assertSessionHasErrors('sources');
        $this->assertDatabaseCount('articles', 10);
        $this->post('/admin/article', $this->story(['body' => 'A research note: <script>alert(1)</script> should always render as escaped text.']))->assertRedirect();
        $this->get('/read/evidence-led-story')->assertOk()->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;', false);
    }

    public function test_chart_uploads_are_validated_and_removed_with_the_article(): void
    {
        Storage::fake('editorial');
        $this->actingAs($this->admin(), 'admin');
        $this->post('/admin/article', $this->story(['image' => UploadedFile::fake()->create('bad.php', 2, 'application/x-php')]))->assertSessionHasErrors('image');
        $this->post('/admin/article', $this->story(['image' => UploadedFile::fake()->image('chart.png')]))->assertRedirect();
        $article = Article::where('slug', 'evidence-led-story')->firstOrFail();
        Storage::disk('editorial')->assertExists(basename($article->image));
        $this->delete('/admin/records/articles/'.$article->id)->assertRedirect('/admin?tab=articles');
        Storage::disk('editorial')->assertMissing(basename($article->image));
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function test_signups_require_consent_deduplicate_and_can_be_exported_and_deleted(): void
    {
        $this->post('/subscribe', ['email' => 'reader@example.com'])->assertSessionHasErrors('consent');
        foreach ([1, 2] as $attempt) {
            $this->post('/subscribe', ['email' => 'READER@example.com', 'consent' => '1'])->assertRedirect('/#newsletter');
        }
        $this->assertDatabaseCount('subscribers', 1);
        $this->actingAs($this->admin(), 'admin');
        Subscriber::create(['email' => '=reader@example.com', 'created_at' => now()->format('Y-m-d H:i:s')]);
        $csv = $this->get('/admin/export')->assertOk()->streamedContent();
        $this->assertStringContainsString('reader@example.com', $csv);
        $this->assertStringContainsString("'=reader@example.com", $csv);
        $this->delete('/admin/records/subscribers/'.Subscriber::first()->id)->assertRedirect('/admin?tab=subscribers');
        $this->assertDatabaseCount('subscribers', 1);
    }

    public function test_enquiries_and_manual_updates_persist(): void
    {
        $this->post('/media-enquiry', ['name' => 'A Reader', 'email' => 'reader@example.com', 'placement' => 'Display placement', 'message' => 'Please share availability for a campaign.'])->assertRedirect('/media-kit#enquire');
        $this->assertDatabaseCount('enquiries', 1);
        $this->actingAs($this->admin(), 'admin');
        $this->get('/admin?tab=enquiries')->assertOk()->assertSee('A Reader');
        $this->post('/admin/snapshot', ['symbol' => 'BTC', 'name' => 'Bitcoin', 'price' => '123.45', 'change_pct' => '-1.25', 'captured_at' => now()->format('Y-m-d\TH:i'), 'source' => 'https://bitcoin.org/'])->assertRedirect();
        $this->get('/bitcoin')->assertOk()->assertSee('$123.45');
        $this->post('/admin/event', ['title' => 'A documented event', 'description' => 'A calendar item with a verifiable primary source.', 'category' => 'Upgrade', 'event_at' => now()->addDay()->format('Y-m-d\TH:i'), 'source' => 'https://ethereum.org/'])->assertRedirect();
        $this->get('/calendar?category=Upgrade')->assertOk()->assertSee('A documented event');
    }

    public function test_settings_password_changes_and_session_expiry(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'admin');
        $data = ['brief_title' => 'A new briefing', 'brief_body' => 'Clearer thinking, saved in Laravel.', 'brief_points' => "One\nTwo\nThree", 'media_audience' => 'Independent readers.', 'media_email' => 'hello@example.com', 'demo' => 'on'];
        $this->post('/admin/settings', $data)->assertRedirect();
        $this->get('/')->assertOk()->assertSee('A new briefing');
        $this->post('/admin/password', ['current_password' => 'wrong', 'new_password' => 'another-safe-password'])->assertSessionHasErrors('current_password');
        $this->post('/admin/password', ['current_password' => 'a-safe-test-password', 'new_password' => 'another-safe-password'])->assertRedirect();
        $this->assertTrue(Hash::check('another-safe-password', $admin->fresh()->password));
        $this->withSession(['admin_seen' => time() - 3601])->get('/admin')->assertRedirect('/admin/login');
        $this->assertGuest('admin');
    }

    public function test_the_media_kit_and_sitemap_are_generated_by_laravel(): void
    {
        $this->get('/media-kit/download')->assertOk()->assertHeader('Content-Disposition', 'attachment; filename="compyvila-media-kit.html"')->assertSee('No audience figures are claimed');
        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=utf-8')->assertSee('/read/');
        $this->get('/search?q=wallet')->assertOk()->assertSee('Your first wallet');
        $this->get('/search?q=not-a-real-topic')->assertOk()->assertSee('No stories found.');
    }

    public function test_social_previews_and_favicons_use_public_absolute_image_metadata(): void
    {
        config(['app.url' => 'https://compyvila.com']);
        $home = $this->get('/')->assertOk();
        $home->assertSee('<meta property="og:image" content="https://compyvila.com/assets/social/compyvila-v4.jpg">', false)
            ->assertSee('<meta property="og:url" content="https://compyvila.com/">', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
            ->assertSee('/favicon.ico')->assertSee('/assets/favicon-v3.svg')->assertSee('/apple-touch-icon.png');
        [$width, $height] = getimagesize(public_path('assets/compyvila-og.png'));
        $this->assertGreaterThan($height, $width);
        $this->assertLessThan(5 * 1024 * 1024, filesize(public_path('assets/compyvila-og.png')));
        $this->get('/search?q=bitcoin')->assertSee('<link rel="canonical" href="https://compyvila.com/search">', false);
        $article = Article::published()->firstOrFail();
        $this->get('/read/'.$article->slug)->assertOk()
            ->assertSee('<meta property="og:type" content="article">', false)
            ->assertSee('property="article:published_time"', false)
            ->assertSee($article->title.' — Compyvila');
        $this->assertSame([180, 180], array_slice(getimagesize(public_path('apple-touch-icon.png')), 0, 2));
        $manifest = json_decode(file_get_contents(public_path('site.webmanifest')), true, flags: JSON_THROW_ON_ERROR);
        foreach ($manifest['icons'] as $icon) {
            $this->assertFileExists(public_path(ltrim($icon['src'], '/')));
        }
    }
}
