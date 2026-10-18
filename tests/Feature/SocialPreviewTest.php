<?php

namespace Tests\Feature;

use App\Support\SocialPreview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_share_images_are_small_public_jpegs_with_accurate_dimensions(): void
    {
        $this->seed();
        config(['app.url'=>'https://compyvila.com']);
        foreach ([null, ...array_map(fn ($file)=>'/assets/articles/'.basename($file),glob(public_path('assets/articles/*.webp')))] as $image) {
            $preview = SocialPreview::image($image);
            $file = public_path(ltrim(parse_url($preview['url'], PHP_URL_PATH),'/'));
            $this->assertSame([1200,630,IMAGETYPE_JPEG],array_slice(getimagesize($file),0,3));
            $this->assertLessThan(280000,filesize($file));
            $this->assertSame('image/jpeg',$preview['type']);
        }
        foreach (['facebookexternalhit/1.1','Twitterbot/1.0','LinkedInBot/1.0','WhatsApp/2.24.8.85'] as $agent) {
            $this->withHeader('User-Agent',$agent)->get('/news')->assertOk()
                ->assertSee('property="og:image:width" content="1200"',false)
                ->assertSee('property="og:image:height" content="630"',false)
                ->assertSee('image/jpeg')->assertSee('https://compyvila.com/assets/social/compyvila-v4.jpg');
        }
        $this->assertSame(SocialPreview::image(null),SocialPreview::image('/../../.env'));
        $this->assertSame(SocialPreview::image(null),SocialPreview::image('https://example.com/image.jpg'));
    }

    public function test_future_uploads_get_persistent_content_versioned_previews_without_cropping(): void
    {
        config(['app.url'=>'https://compyvila.com']);
        $source = public_path('uploads/social-preview-test.png');
        $outputs = [];
        try {
            $image = imagecreatetruecolor(80,160);
            imagefill($image,0,0,imagecolorallocate($image,200,30,50));
            imagepng($image,$source);
            $first = SocialPreview::image('/uploads/social-preview-test.png');
            $outputs[] = public_path(ltrim(parse_url($first['url'],PHP_URL_PATH),'/'));
            $this->assertStringContainsString('/uploads/social/',$first['url']);
            $this->assertSame($first,SocialPreview::image('/uploads/social-preview-test.png'));
            $result = imagecreatefromjpeg($outputs[0]);
            $center = imagecolorsforindex($result,imagecolorat($result,600,315));
            $edge = imagecolorsforindex($result,imagecolorat($result,10,315));
            $this->assertGreaterThan(180,$center['red']);
            $this->assertLessThan(60,$center['green']);
            $this->assertGreaterThan(230,$edge['green']);
            imagefill($image,0,0,imagecolorallocate($image,20,70,200));
            imagepng($image,$source);
            $second = SocialPreview::image('/uploads/social-preview-test.png');
            $outputs[] = public_path(ltrim(parse_url($second['url'],PHP_URL_PATH),'/'));
            $this->assertNotSame($first['url'],$second['url']);
        } finally {
            foreach ([$source,...$outputs] as $file) if (is_file($file)) unlink($file);
        }
    }
}
