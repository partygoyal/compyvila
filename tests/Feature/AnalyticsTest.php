<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_tag_nonce_matches_csp_and_admin_remains_untracked(): void
    {
        $this->seed();
        config(['analytics.enabled'=>true, 'analytics.measurement_id'=>'G-0E51GY5RYX']);
        $response = $this->get('/projects')->assertOk()->assertSee('gtag/js?id=G-0E51GY5RYX', false);
        preg_match('/<script nonce="([^"]+)">/', $response->getContent(), $match);
        $nonce = $match[1];
        $this->assertStringContainsString("'nonce-".$nonce."'", $response->headers->get('Content-Security-Policy'));
        $this->assertStringNotContainsString('unsafe-inline', $response->headers->get('Content-Security-Policy'));
        $this->assertStringContainsString('https://*.google-analytics.com', $response->headers->get('Content-Security-Policy'));
        $next = $this->get('/')->assertOk();
        $this->assertStringNotContainsString('nonce="'.$nonce.'"', $next->getContent());
        $this->get('/privacy')->assertOk()->assertSee('Google Analytics')->assertDontSee('We do not include third-party analytics');
        $admin = $this->get('/admin/login')->assertOk()->assertDontSee('googletagmanager.com');
        $this->assertStringNotContainsString('google-analytics.com', $admin->headers->get('Content-Security-Policy'));
    }

    public function test_disabled_analytics_does_not_render_tracking_or_allow_google_origins(): void
    {
        $this->seed();
        config(['analytics.enabled'=>false]);
        $response = $this->get('/')->assertOk()->assertDontSee('gtag/js?id=');
        $this->assertStringNotContainsString('googletagmanager.com', $response->headers->get('Content-Security-Policy'));
        $this->get('/privacy')->assertOk()->assertDontSee('<h2>Google Analytics</h2>', false);
    }
}
