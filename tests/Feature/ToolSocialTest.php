<?php

namespace Tests\Feature;

use App\Support\ToolSeo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToolSocialTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_tool_and_quiz_previews_use_unique_compact_public_jpegs(): void
    {
        $this->seed();
        config(['app.url' => 'https://compyvila.com']);
        $images = json_decode(file_get_contents(resource_path('data/tool-social.json')), true, flags: JSON_THROW_ON_ERROR);
        $this->assertCount(13, $images);
        $this->assertCount(13, array_unique(array_column($images, 'path')));
        foreach ($images as $key => $image) {
            $file = public_path($image['path']);
            $this->assertFileExists($file);
            $this->assertLessThan(100000, filesize($file));
            $info = getimagesize($file);
            $this->assertSame([1200, 630], array_slice($info, 0, 2));
            $this->assertSame('image/jpeg', $info['mime']);
            $path = $key === 'directory' ? '/tools' : ($key === 'quiz' ? '/quiz' : '/tools/'.$key);
            $url = 'https://compyvila.com'.$image['path'];
            $this->get($path)->assertOk()->assertSee('<meta property="og:image" content="'.$url.'">', false)
                ->assertSee('<meta name="twitter:image" content="'.$url.'">', false)
                ->assertSee('<meta property="og:image:width" content="1200">', false)
                ->assertSee('<meta property="og:image:height" content="630">', false)
                ->assertSee('<meta property="og:image:type" content="image/jpeg">', false)
                ->assertSee('<meta property="og:image:alt" content="'.e($image['alt']).'">', false);
            $page = collect(ToolSeo::data($key)['pageSchema']['@graph'])->first(fn ($item) => in_array($item['@type'], ['WebPage', 'CollectionPage']));
            $this->assertSame($url, $page['image']);
        }
        $this->get('/')->assertDontSee('/assets/social/tools/');
    }
}
