<?php
namespace Tests\Feature;

use App\Services\MarketPrices;
use Illuminate\Support\Facades\{Cache, Http};
use Tests\TestCase;

class MarketPricesTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        config(['market.key'=>'test-secret', 'market.cache_store'=>'array']);
        Cache::store('array')->flush();
        Http::preventStrayRequests();
    }

    private function payload(): array
    {
        $data = [];
        foreach (MarketPrices::ASSETS as $id=>$symbol) {
            $data[$id] = ['symbol'=>$symbol, 'name'=>$symbol, 'slug'=>strtolower($symbol), 'quote'=>['USD'=>[
                'price'=>123.45, 'percent_change_24h'=>-2.1, 'last_updated'=>now()->toAtomString(),
            ]]];
        }
        return ['status'=>['error_code'=>0], 'data'=>$data];
    }

    public function test_batch_quotes_are_cached_and_expired_provider_data_is_labelled(): void
    {
        Http::fake(['pro-api.coinmarketcap.com/*'=>Http::response($this->payload())]);
        $service = new MarketPrices;
        $this->assertCount(20, $service->quotes());
        $this->assertFalse($service->quotes()->first()['stale']);
        Http::assertSentCount(1);
        Http::assertSent(fn ($r)=>$r->hasHeader('X-CMC_PRO_API_KEY','test-secret') && $r['id']===implode(',',array_keys(MarketPrices::ASSETS)));
        $this->travel(16)->minutes();
        Http::fake(['pro-api.coinmarketcap.com/*'=>Http::response([],429)]);
        $this->assertTrue($service->quotes()->first()['stale']);
        $this->assertSame(123.45, $service->quotes()->first()['price']);
        Http::assertSentCount(1);
    }

    public function test_failure_without_verified_data_returns_no_demo_prices_and_backs_off(): void
    {
        Http::fake(['pro-api.coinmarketcap.com/*'=>Http::response([],500)]);
        $service = new MarketPrices;
        $this->assertTrue($service->quotes()->isEmpty());
        $this->assertTrue($service->quotes()->isEmpty());
        Http::assertSentCount(1);
    }

    public function test_public_pages_use_verified_quotes_without_exposing_key(): void
    {
        $this->seed();
        Http::fake(['pro-api.coinmarketcap.com/*'=>Http::response($this->payload())]);
        foreach (['/','/bitcoin','/ethereum','/market-notes'] as $url) {
            $this->get($url)->assertOk()->assertSee('123.45')->assertSee('COINMARKETCAP')->assertDontSee('test-secret')->assertDontSee('ILLUSTRATIVE');
        }
        $this->get('/market-notes')->assertViewHas('snapshots', fn ($quotes) => $quotes->pluck('symbol')->all() === array_values(MarketPrices::ASSETS));
        Http::assertSentCount(1);
    }

    public function test_incomplete_response_does_not_replace_last_good_batch(): void
    {
        Cache::store('array')->forever('market.cmc.v2.quotes', [['symbol'=>'BTC','price'=>99,'captured_at'=>now()->toDateTimeString()]]);
        $data=$this->payload(); unset($data['data'][1839]);
        Http::fake(['pro-api.coinmarketcap.com/*'=>Http::response($data)]);
        $this->assertSame(99,(new MarketPrices)->quotes()->first()['price']);
    }
}
