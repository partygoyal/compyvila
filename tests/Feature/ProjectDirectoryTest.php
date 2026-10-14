<?php

namespace Tests\Feature;

use App\Models\{Admin, Project};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectDirectoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function data(array $overrides = []): array
    {
        return array_merge([
            'name'=>'Example Research Network', 'slug'=>'example-research-network',
            'tagline'=>'An illustrative network used only in automated tests.',
            'description'=>"## Research notes\n\nThis is an automated test fixture, not a real project listing.\n\n[Documentation](https://example.com/docs)",
            'category'=>'Infrastructure', 'network'=>'Ethereum', 'symbol'=>'', 'launch_year'=>2026,
            'milestone'=>'Testnet launch', 'launch_date'=>'2026-06-01', 'stage'=>'Testnet',
            'website'=>'https://example.com/', 'sources'=>'https://example.com/docs', 'status'=>'published',
        ], $overrides);
    }

    private function project(array $overrides = []): Project
    {
        return Project::create(array_merge($this->data(), ['published_at'=>now()->subMinute()], $overrides));
    }

    private function login(): void
    {
        $this->actingAs(Admin::create(['email'=>'projects@example.com','password'=>'safe-test-password']), 'admin');
    }

    public function test_empty_directory_and_editorial_forms_are_ready_without_sample_listings(): void
    {
        $this->get('/projects')->assertOk()->assertSee('The directory starts here.')
            ->assertSee('2025')->assertSee('2026')->assertSee('Suggest a project');
        $this->assertDatabaseCount('projects', 0);
        $this->get('/admin/project')->assertRedirect('/admin/login');
        $this->post('/admin/project', $this->data())->assertRedirect('/admin/login');
        $this->delete('/admin/records/projects/1')->assertRedirect('/admin/login');
        $this->login();
        $this->get('/admin?tab=projects')->assertOk()->assertSee('No projects yet.');
        $this->get('/admin/project')->assertOk()->assertSee('Launch milestone year');
    }

    public function test_search_combines_year_category_and_stage_and_hides_unpublished_projects(): void
    {
        $this->project();
        $this->project(['slug'=>'older-payments','name'=>'Older Payments','launch_year'=>2025,'launch_date'=>null,'category'=>'Payments','stage'=>'Live']);
        $this->project(['slug'=>'private-draft','name'=>'Private Draft','status'=>'draft']);
        $this->project(['slug'=>'future-listing','name'=>'Future Listing','published_at'=>now()->addDay()]);
        $this->get('/projects?year=2026&category=Infrastructure&stage=Testnet&q=Ethereum')
            ->assertOk()->assertSee('Example Research Network')->assertDontSee('Older Payments')
            ->assertDontSee('Private Draft')->assertDontSee('Future Listing');
        $this->get('/projects?year=2025')->assertOk()->assertSee('Older Payments')->assertDontSee('Example Research Network');
        $this->get('/projects?q=does-not-exist')->assertOk()->assertSee('No projects match those filters.');
        $this->get('/projects/private-draft')->assertNotFound();
        $this->get('/projects/future-listing')->assertNotFound();
    }

    public function test_listing_details_escape_content_and_sitemap_only_includes_public_projects(): void
    {
        config(['app.url'=>'https://compyvila.com']);
        $project = $this->project(['name'=>'Example <script>bad</script>', 'description'=>"<script>alert(1)</script>\n\n## Source notes\n\n[Source](https://example.com/docs)"]);
        $this->project(['slug'=>'related-fixture','name'=>'Related Project']);
        $this->project(['slug'=>'private-draft','status'=>'draft']);
        $html = $this->get('/projects/'.$project->slug)->assertOk()
            ->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('Testnet launch')->assertSee('Jun 1, 2026')->assertSee('LAST REVIEWED')
            ->assertSee('href="https://example.com/docs"', false)->getContent();
        preg_match('~<script type="application/ld\+json">(.*?)</script>~s', $html, $match);
        $schema = json_decode($match[1], true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame($project->name, $schema['@graph'][0]['about']['name']);
        $this->assertSame('WebPage', $schema['@graph'][0]['@type']);
        $this->get('/sitemap.xml')->assertOk()->assertSee('https://compyvila.com/projects</loc>', false)
            ->assertSee('/projects/'.$project->slug)->assertDontSee('/projects/private-draft');
    }

    public function test_editor_can_create_edit_unpublish_and_delete_a_listing(): void
    {
        $this->login();
        $this->post('/admin/project', $this->data(['slug'=>'']))->assertRedirect('/admin?tab=projects');
        $project = Project::firstOrFail();
        $this->assertSame('example-research-network', $project->slug);
        $published = $project->published_at->toAtomString();
        $this->get('/admin/project?id='.$project->id)->assertOk()->assertSee('Example Research Network');
        $this->post('/admin/project', $this->data(['id'=>$project->id, 'name'=>'Updated Research Network', 'status'=>'draft']))->assertRedirect();
        $this->get('/projects/'.$project->slug)->assertNotFound();
        $this->post('/admin/project', $this->data(['id'=>$project->id]))->assertRedirect();
        $this->assertSame($published, $project->fresh()->published_at->toAtomString());
        $this->delete('/admin/records/projects/'.$project->id)->assertRedirect('/admin?tab=projects');
        $this->assertDatabaseCount('projects', 0);
    }

    public function test_editorial_validation_requires_sources_safe_urls_and_a_matching_milestone_year(): void
    {
        $this->login();
        foreach ([['sources'=>''], ['sources'=>'javascript:alert(1)'], ['website'=>'javascript:alert(1)'],
            ['launch_year'=>2024], ['launch_date'=>'2025-06-01'], ['category'=>'Made up'], ['stage'=>'Guaranteed profit']] as $overrides) {
            $field = array_key_first($overrides);
            $this->post('/admin/project', $this->data($overrides))->assertSessionHasErrors($field);
        }
        $this->assertDatabaseCount('projects', 0);
        $this->post('/admin/project', $this->data(['status'=>'draft','sources'=>'','launch_date'=>'']))->assertRedirect();
        $this->post('/admin/project', $this->data())->assertSessionHasErrors('slug');
    }

    public function test_pagination_preserves_filters_and_each_public_profile_remains_discoverable(): void
    {
        for ($i=1; $i<=13; $i++) {
            $this->project(['name'=>'Fixture '.$i,'slug'=>'fixture-'.$i]);
        }
        $this->get('/projects?year=2026&stage=Testnet')->assertOk()
            ->assertViewHas('projects', fn ($projects)=>$projects->count()===12 && $projects->total()===13)
            ->assertSee('year=2026&amp;stage=Testnet&amp;page=2', false);
        $this->get('/projects?year=2026&stage=Testnet&page=2')->assertOk()
            ->assertViewHas('projects', fn ($projects)=>$projects->count()===1);
    }
}
