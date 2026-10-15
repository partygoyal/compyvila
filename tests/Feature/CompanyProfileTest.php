<?php
namespace Tests\Feature;

use App\Models\{Admin,Article};
use App\Support\SocialPreview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_publishes_with_technology_topic_sources_and_share_banner_and_preserves_edits(): void
    {
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-10-15 16:00:00'));
        $this->seed();
        $this->artisan('compyvila:publish-company-profiles')->assertExitCode(0);
        $this->assertDatabaseCount('articles',10);
        $this->artisan('compyvila:publish-company-profiles',['--publish'=>true])->assertExitCode(0);
        $this->assertDatabaseCount('articles',11);
        $article=Article::where('slug','esperto-technologies-saas-development-india')->firstOrFail();
        $this->assertSame('Technology',$article->coin);
        $this->assertSame('Learn',$article->category);
        $this->get('/read/'.$article->slug)->assertOk()->assertSee('Company profile.')
            ->assertSee('href="https://www.espertotech.in/services/saas-development"',false)->assertSee('article-banner');
        $this->get('/learn')->assertSee($article->title);
        $this->get('/altcoins')->assertDontSee($article->title);
        $this->get('/')->assertSee($article->image);
        $this->get('/sitemap.xml')->assertSee('/read/'.$article->slug);
        $this->assertStringContainsString('/assets/social/article-',SocialPreview::image($article->image)['url']);
        $this->actingAs(Admin::create(['email'=>'profile-editor@example.com','password'=>'safe-test-password']),'admin');
        $this->get('/admin/article?id='.$article->id)->assertSee('value="Technology" selected',false);
        $data=$article->toArray();unset($data['image'],$data['featured']);
        $data['title']='Esperto Technologies: an edited company profile';
        $this->post('/admin/article',$data)->assertRedirect('/admin?tab=articles');
        $this->assertSame($data['title'],$article->fresh()->title);
        $this->artisan('compyvila:publish-company-profiles',['--publish'=>true])->assertExitCode(0);
        $this->assertDatabaseCount('articles',11);
        $this->assertSame($data['title'],$article->fresh()->title);
    }
}
