<?php

namespace Tests\Feature;

use App\Support\ToolCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToolsTest extends TestCase
{
    use RefreshDatabase;

    public function test_directory_and_each_tool_are_public_indexable_and_linked(): void
    {
        $this->seed();
        $this->assertCount(12, ToolCatalog::all());
        $directory = $this->get('/tools')->assertOk()->assertSee('A clearer picture.');
        $sitemap = $this->get('/sitemap.xml')->assertOk();
        foreach (ToolCatalog::all() as $tool) {
            $path = '/tools/'.$tool['slug'];
            $directory->assertSee('href="'.$path.'"', false);
            $sitemap->assertSee($path);
            $response = $this->get($path)->assertOk()->assertSee($tool['title'])->assertSee($tool['formula'])
                ->assertSee('type="module" src="/assets/tools.js', false)->assertDontSee('name="robots" content="noindex', false);
            $response->assertHeader('Content-Security-Policy');
        }
        $this->get('/tools/not-a-tool')->assertNotFound();
        $this->get('/')->assertSee('href="/tools"', false)->assertDontSee('/assets/tools.js');
    }
}
