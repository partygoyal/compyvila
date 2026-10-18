<?php
namespace Tests\Feature;

use App\Models\{Admin,CreatorApplication};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatorNetworkTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void { parent::setUp(); $this->seed(); }
    private function application(array $overrides=[]): array
    {
        return array_merge(['name'=>'Crypto Educator','email'=>'creator@example.com','phone'=>'+91 1234567890','platform'=>'YouTube','profile_url'=>'https://www.youtube.com/@example','followers'=>12500,'location'=>'Mumbai, India','languages'=>'English, Hindi','topics'=>['Bitcoin','Crypto education'],'formats'=>['Sponsored videos'],'rate'=>'INR 25,000 per video','message'=>'I make practical crypto explainers.','consent'=>'1','website'=>''],$overrides);
    }
    public function test_application_page_is_discoverable_and_successful_lead_is_private(): void
    {
        $this->get('/creator-network')->assertOk()->assertSee('Join the creator network')->assertSee('name="consent"',false);
        $this->get('/')->assertSee('/creator-network');
        $this->get('/media-kit')->assertSee('/creator-network');
        $this->get('/sitemap.xml')->assertSee('/creator-network');
        $this->post('/creator-network',$this->application(['status'=>'approved','notes'=>'Injected note']))->assertRedirect('/creator-network#apply')->assertSessionHas('creator_success');
        $lead=CreatorApplication::firstOrFail();
        $this->assertSame('new',$lead->status);
        $this->assertNull($lead->notes);
        $this->assertSame(['Bitcoin','Crypto education'],$lead->topics);
        $this->assertNotNull($lead->consented_at);
        $this->get('/creator-network')->assertSee('You’re on our radar.')->assertDontSee('creator@example.com');
        $this->get('/admin/creators')->assertRedirect('/admin/login');
        $this->get('/admin/creators/'.$lead->id)->assertRedirect('/admin/login');
        $this->patch('/admin/creators/'.$lead->id,['status'=>'approved'])->assertRedirect('/admin/login');
    }
    public function test_invalid_links_missing_consent_bot_input_and_invalid_topics_are_rejected(): void
    {
        foreach ([['profile_url'=>'javascript:alert(1)'],['consent'=>'0'],['website'=>'spam'],['topics'=>['Invalid']]] as $changes) {
            $this->post('/creator-network',$this->application($changes))->assertSessionHasErrors();
        }
        $this->assertDatabaseCount('creator_applications',0);
    }
    public function test_duplicate_profiles_are_not_inserted_twice(): void
    {
        $this->post('/creator-network',$this->application())->assertSessionHasNoErrors();
        $this->post('/creator-network',$this->application(['email'=>'CREATOR@EXAMPLE.COM','profile_url'=>'https://www.youtube.com/@example/']))->assertSessionHasErrors('email');
        $this->assertDatabaseCount('creator_applications',1);
    }
    public function test_admin_can_find_review_update_and_delete_a_lead(): void
    {
        $this->post('/creator-network',$this->application(['message'=>'<script>bad()</script>']))->assertSessionHasNoErrors();
        $lead=CreatorApplication::firstOrFail();
        $this->actingAs(Admin::create(['email'=>'creator-admin@example.com','password'=>'a-safe-test-password']),'admin');
        $this->get('/admin')->assertSee('CREATOR LEADS');
        $this->get('/admin/creators?q=Educator&status=new')->assertOk()->assertSee('creator@example.com');
        $this->get('/admin/creators?q=Nobody')->assertSee('No applications match');
        $this->get('/admin/creators/'.$lead->id)->assertOk()->assertSee('INR 25,000 per video')->assertSee('&lt;script&gt;bad()',false)->assertDontSee('<script>bad()',false);
        $this->patch('/admin/creators/'.$lead->id,['status'=>'contacted','notes'=>'Discuss a YouTube campaign.'])->assertSessionHasNoErrors();
        $this->assertSame('contacted',$lead->fresh()->status);
        $this->assertSame('Discuss a YouTube campaign.',$lead->fresh()->notes);
        $this->patch('/admin/creators/'.$lead->id,['status'=>'invalid'])->assertSessionHasErrors('status');
        $this->get('/admin/creators?status=new')->assertDontSee('creator@example.com');
        $this->delete('/admin/creators/'.$lead->id)->assertRedirect('/admin/creators');
        $this->assertDatabaseCount('creator_applications',0);
    }
}
