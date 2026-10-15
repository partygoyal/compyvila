<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectCollectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_collection_validates_without_writes_and_preserves_edited_listings_on_reimport(): void
    {
        $this->seed();
        $this->artisan('compyvila:publish-projects')->assertExitCode(0);
        $this->assertDatabaseCount('projects', 0);
        $this->artisan('compyvila:publish-projects', ['--publish'=>true])->assertExitCode(0);
        $this->assertDatabaseCount('projects', 6);
        $project = Project::where('slug','unichain')->firstOrFail();
        $project->update(['description'=>'An editor has made a correction.', 'status'=>'draft']);
        $reviewed = $project->updated_at->toAtomString();
        $this->travel(1)->days();
        $this->artisan('compyvila:publish-projects', ['--publish'=>true])->assertExitCode(0);
        $this->assertDatabaseCount('projects', 6);
        $this->assertSame('draft', $project->fresh()->status);
        $this->assertSame('An editor has made a correction.', $project->fresh()->description);
        $this->assertSame($reviewed, $project->fresh()->updated_at->toAtomString());
    }

    public function test_sourced_profiles_show_the_correct_milestone_and_working_related_guide_links(): void
    {
        $this->seed();
        $this->artisan('compyvila:publish-guides', ['--publish'=>true])->assertExitCode(0);
        $this->artisan('compyvila:publish-projects', ['--publish'=>true])->assertExitCode(0);
        $projects = json_decode(file_get_contents(base_path('content/projects-2025.json')), true, flags: JSON_THROW_ON_ERROR);
        $index = $this->get('/projects?year=2025')->assertOk()->assertSee('6 PROJECTS TO EXPLORE');
        foreach ($projects as $project) {
            $index->assertSee($project['name']);
            $html = $this->get('/projects/'.$project['slug'])->assertOk()->assertSee('What to research')
                ->assertSee('ORIGINAL SOURCES')->assertSee($project['milestone'])->getContent();
            foreach ($project['sources'] as $source) {
                $this->assertStringContainsString('href="'.e($source).'"', $html);
            }
            preg_match_all('~href="(/read/[^"#]+)"~', $html, $links);
            foreach (array_unique($links[1]) as $link) {
                $this->get($link)->assertOk();
            }
            $this->get('/sitemap.xml')->assertSee('/projects/'.$project['slug']);
        }
        $this->get('/projects?year=2026')->assertOk()->assertSee('No projects match those filters.');
        $this->assertNull(Project::where('slug','initia')->firstOrFail()->launch_date);
        $this->assertSame('Product launch', Project::where('slug','uniswap-v4')->firstOrFail()->milestone);
    }
}
