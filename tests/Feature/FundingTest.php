<?php
namespace Tests\Feature;
use App\Models\{Admin,FundingRound,FundingStartup,FundingSubmission};
use App\Support\FundingPublisher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class FundingTest extends TestCase {
    use RefreshDatabase;
    protected function setUp(): void { parent::setUp();$this->seed(); }
    private function published(): void { FundingPublisher::publish(true); }
    private function admin(): void { $this->actingAs(Admin::create(['email'=>'funding-editor@example.com','password'=>'a-safe-test-password']),'admin'); }
    private function values(array $overrides=[]): array {
        $startup=FundingStartup::firstOrCreate(['slug'=>'test-startup'],['name'=>'Test Startup','sector'=>'Infrastructure','description'=>'A company building infrastructure for blockchain applications.','website'=>'https://example.com']);
        return array_merge(['funding_startup_id'=>$startup->id,'slug'=>'test-round-2025','announced_on'=>'2025-03-01','stage'=>'Seed','type'=>'Venture','amount_usd'=>'1234567.89','valuation_usd'=>'','valuation_basis'=>'Undisclosed','lead_investors'=>'Example Ventures','other_investors'=>'','summary'=>'A sourced announcement of new funding for a blockchain infrastructure business.','sources'=>'https://example.com/announcement','source_notes'=>'','notes'=>'private-review-note','status'=>'published','reviewed'=>'1'],$overrides);
    }
    public function test_public_tracker_filters_totals_round_history_and_sources(): void {
        $this->published();$this->assertDatabaseCount('funding_rounds',23);$this->assertDatabaseCount('funding_startups',18);
        $this->get('/funding')->assertOk()->assertSee('Funding tracked',false)->assertSee('funding.css');
        $expected=FundingRound::published()->where('type','Venture')->sum('amount_usd');
        $this->get('/funding')->assertViewHas('stats',fn($s)=>$s['rounds']===23&&(float)$s['venture']===(float)$expected);
        $this->get('/funding?type=Private%20token%20sale')->assertViewHas('stats',fn($s)=>$s['rounds']===1&&(float)$s['venture']===0.0&&(float)$s['other']===140000000.0)->assertSee('Walrus');
        $this->get('/funding?year=2026&sector=Payments&stage=Series%20A&investor=QED')->assertViewHas('rounds',fn($r)=>$r->total()===1&&$r->first()->startup->slug==='kast');
        $this->get('/funding?q=redotpay')->assertViewHas('rounds',fn($r)=>$r->total()===3);
        $this->get('/funding?q=no-matching-startup')->assertSee('No rounds match this view.');
        $this->get('/funding/privy-undisclosed-2025-03-19')->assertOk()->assertSee('Undisclosed')->assertSee('privy.io')->assertDontSee('private-review-note');
        $this->get('/funding/rain-series-b-2025-08-28')->assertOk()->assertViewHas('history',fn($r)=>$r->count()===3);
        $this->get('/sitemap.xml')->assertSee('/funding/rain-series-b-2025-08-28');
        $this->get('/funding/no-such-round')->assertNotFound();
    }
    public function test_publisher_is_idempotent_and_preserves_edits_and_drafts(): void {
        $this->published();$round=FundingRound::first();$round->update(['summary'=>'Edited funding summary must survive repeat publication.','status'=>'draft']);
        $result=FundingPublisher::publish(true);$this->assertSame(0,$result['created']);$this->assertSame('draft',$round->fresh()->status);$this->assertSame('Edited funding summary must survive repeat publication.',$round->fresh()->summary);
        $this->get('/funding/'.$round->slug)->assertNotFound();$this->get('/sitemap.xml')->assertDontSee('/funding/'.$round->slug);
        FundingRound::where('status','published')->first()->update(['announced_on'=>today()->addDay()]);
        $this->get('/funding')->assertViewHas('stats',fn($s)=>$s['rounds']===21);
    }
    public function test_admin_authorization_validation_review_and_crud(): void {
        $this->get('/admin/funding')->assertRedirect('/admin/login');$this->post('/admin/funding/round',$this->values())->assertRedirect('/admin/login');
        $this->admin();
        $this->get('/admin/funding')->assertOk();$this->get('/admin/funding/startup')->assertOk();$this->get('/admin/funding/round')->assertOk();
        $v=$this->values();unset($v['reviewed']);$this->post('/admin/funding/round',$v)->assertSessionHasErrors('reviewed');
        $this->post('/admin/funding/round',$this->values(['amount_usd'=>0]))->assertSessionHasErrors('amount_usd');
        $this->post('/admin/funding/round',$this->values(['sources'=>'javascript:alert(1)']))->assertSessionHasErrors('sources');
        $this->post('/admin/funding/round',$this->values(['valuation_usd'=>5000000]))->assertSessionHasErrors('valuation_basis');
        $this->post('/admin/funding/round',$this->values(['announced_on'=>today()->addDay()->toDateString()]))->assertSessionHasErrors('announced_on');
        $this->post('/admin/funding/round',$this->values())->assertRedirect('/admin/funding');
        $round=FundingRound::where('slug','test-round-2025')->firstOrFail();$this->assertSame('1234567.89',$round->amount_usd);$this->assertNotNull($round->checked_at);
        $this->get('/funding/'.$round->slug)->assertOk()->assertDontSee('private-review-note');
        $this->get('/admin/funding/round?id='.$round->id)->assertOk();
        $this->post('/admin/funding/round',$this->values(['id'=>$round->id,'status'=>'draft']))->assertRedirect();$this->get('/funding/'.$round->slug)->assertNotFound();
        $this->delete('/admin/funding/round/'.$round->id,['confirm'=>1])->assertRedirect('/admin/funding');$this->assertDatabaseMissing('funding_rounds',['id'=>$round->id]);
        $draft=$this->values(['status'=>'draft','slug'=>'new-draft']);unset($draft['reviewed']);$this->post('/admin/funding/round',$draft)->assertRedirect('/admin/funding')->assertSessionHasNoErrors();
        $this->post('/admin/funding/startup',['name'=>'New Startup','slug'=>'','sector'=>'DeFi','description'=>'A new startup profile with sufficient description.','website'=>'https://example.org'])->assertRedirect('/admin/funding');
        $this->assertDatabaseHas('funding_startups',['slug'=>'new-startup']);
    }
    public function test_submissions_are_private_reviewable_and_do_not_auto_publish(): void {
        $v=['name'=>'A Founder','email'=>'FOUNDER@example.com','startup_name'=>'New <script>Startup</script>','website'=>'https://example.com','announcement_url'=>'https://example.com/funding','message'=>'Please review our funding.','consent'=>'1','status'=>'published','notes'=>'untrusted'];
        $this->post('/funding/submit',array_diff_key($v,['consent'=>1]))->assertSessionHasErrors('consent');
        $this->post('/funding/submit',array_merge($v,['company_fax'=>'spam']))->assertSessionHasErrors('company_fax');
        $this->post('/funding/submit',$v)->assertRedirect('/funding#submit');
        $lead=FundingSubmission::firstOrFail();$this->assertSame('new',$lead->status);$this->assertSame('founder@example.com',$lead->email);$this->assertNull($lead->notes);$this->assertDatabaseCount('funding_rounds',0);
        $this->get('/funding')->assertDontSee('founder@example.com')->assertDontSee('Please review our funding.');
        $this->admin();$this->get('/admin/funding/submissions/'.$lead->id)->assertOk()->assertSee('&lt;script&gt;',false);
        $this->patch('/admin/funding/submissions/'.$lead->id,['status'=>'reviewed','notes'=>'Internal review'])->assertRedirect();$this->assertSame('reviewed',$lead->fresh()->status);
        $this->delete('/admin/funding/submissions/'.$lead->id,['confirm'=>1])->assertRedirect('/admin/funding');$this->assertDatabaseMissing('funding_submissions',['id'=>$lead->id]);
    }
}
