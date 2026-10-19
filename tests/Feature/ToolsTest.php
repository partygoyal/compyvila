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
        config(['app.url' => 'https://compyvila.com']);
        $this->assertCount(11, ToolCatalog::all());
        $directory = $this->get('/tools')->assertOk()->assertSee('A clearer picture.');
        $directory->assertDontSee('data-calculator="crypto-knowledge-quiz"', false)->assertDontSee('href="/tools/crypto-knowledge-quiz"', false);
        $this->get('/tools/crypto-knowledge-quiz')->assertStatus(301)->assertRedirect('/quiz');
        $this->get('/quiz')->assertOk()->assertSee('id="quiz"', false)->assertSee('href="https://compyvila.com/quiz"', false)->assertSee('nav-quiz active')->assertSee('/assets/tools.js');
        $sitemap = $this->get('/sitemap.xml')->assertOk()->assertSee('/quiz')->assertDontSee('/tools/crypto-knowledge-quiz');
        foreach (ToolCatalog::all() as $tool) {
            $path = '/tools/'.$tool['slug'];
            $directory->assertSee('href="'.$path.'"', false);
            $sitemap->assertSee($path);
            $response = $this->get($path)->assertOk()->assertSee(\App\Support\ToolSeo::content($tool['slug'])['title'])->assertSee($tool['formula'])
                ->assertSee('type="module" src="/assets/tools.js', false)->assertDontSee('name="robots" content="noindex', false);
            $response->assertHeader('Content-Security-Policy');
        }
        $this->get('/tools/not-a-tool')->assertNotFound();
        $this->get('/')->assertSee('href="/tools"', false)->assertDontSee('/assets/tools.js');
    }
}
