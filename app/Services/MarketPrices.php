<?php

namespace App\Services;

use App\Models\Snapshot;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class MarketPrices
{
    public function quotes(): Collection
    {
        if (! config('market.key')) {
            return Snapshot::orderBy('id')->get();
        }
        $cache = Cache::store(config('market.cache_store'));
        if (! $cache->has('market.cmc.attempt')) {
            $lock = $cache->lock('market.cmc.refresh', 20);
            if ($lock->get()) {
                try {
                    if (! $cache->has('market.cmc.attempt')) {
                        // Bound calls even when the provider fails; never expose credentials or response errors.
                        $cache->put('market.cmc.attempt', true, 300);
                        $response = Http::withHeaders(['X-CMC_PRO_API_KEY' => config('market.key')])
                            ->connectTimeout(3)->timeout(6)->get('https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest', [
                                'id' => '1,1027,5426,1839', 'convert' => 'USD',
                            ])->throw()->json();
                        if (($response['status']['error_code'] ?? -1) !== 0) {
                            throw new \RuntimeException('Market provider unavailable');
                        }
                        $quotes = [];
                        foreach ([1 => 'BTC', 1027 => 'ETH', 5426 => 'SOL', 1839 => 'BNB'] as $id => $symbol) {
                            $asset = $response['data'][$id] ?? [];
                            $quote = $asset['quote']['USD'] ?? [];
                            if (($asset['symbol'] ?? '') !== $symbol || ! is_numeric($quote['price'] ?? null)
                                || $quote['price'] <= 0 || ! is_numeric($quote['percent_change_24h'] ?? null)
                                || empty($quote['last_updated'])) {
                                throw new \RuntimeException('Incomplete market response');
                            }
                            $observed = Carbon::parse($quote['last_updated']);
                            if ($observed->greaterThan(now()->addMinutes(5))) {
                                throw new \RuntimeException('Invalid market timestamp');
                            }
                            $quotes[] = ['symbol' => $symbol, 'name' => $asset['name'], 'price' => (float) $quote['price'],
                                'change_pct' => (float) $quote['percent_change_24h'],
                                'captured_at' => $observed->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s'),
                                'source' => 'https://coinmarketcap.com/currencies/'.['BTC'=>'bitcoin','ETH'=>'ethereum','SOL'=>'solana','BNB'=>'bnb'][$symbol].'/',
                            ];
                        }
                        $cache->forever('market.cmc.quotes', $quotes);
                    }
                } catch (\Throwable $error) {
                    // Preserve verified data. Manual/demo prices must never substitute for failed API quotes.
                } finally {
                    $lock->release();
                }
            }
        }
        return collect($cache->get('market.cmc.quotes', []))->map(function ($quote) {
            $quote['stale'] = Carbon::parse($quote['captured_at'])->lt(now()->subMinutes(15));
            return $quote;
        });
    }
}
