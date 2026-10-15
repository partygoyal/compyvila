<?php

namespace App\Services;

use App\Models\Snapshot;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class MarketPrices
{
    public const ASSETS = [
        1 => 'BTC', 1027 => 'ETH', 5426 => 'SOL', 1839 => 'BNB',
        52 => 'XRP', 74 => 'DOGE', 2010 => 'ADA', 1958 => 'TRX',
        5805 => 'AVAX', 1975 => 'LINK', 6636 => 'DOT', 2 => 'LTC',
        1831 => 'BCH', 7083 => 'UNI', 6535 => 'NEAR', 20947 => 'SUI',
        21794 => 'APT', 22974 => 'TAO', 5690 => 'RENDER', 7278 => 'AAVE',
    ];

    public function quotes(): Collection
    {
        if (! config('market.key')) {
            return Snapshot::orderBy('id')->get();
        }
        $cache = Cache::store(config('market.cache_store'));
        if (! $cache->has('market.cmc.v2.attempt')) {
            $lock = $cache->lock('market.cmc.v2.refresh', 20);
            if ($lock->get()) {
                try {
                    if (! $cache->has('market.cmc.v2.attempt')) {
                        // Bound calls even when the provider fails; never expose credentials or response errors.
                        $cache->put('market.cmc.v2.attempt', true, 300);
                        $response = Http::withHeaders(['X-CMC_PRO_API_KEY' => config('market.key')])
                            ->connectTimeout(3)->timeout(6)->get('https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest', [
                                'id' => implode(',', array_keys(self::ASSETS)), 'convert' => 'USD',
                            ])->throw()->json();
                        if (($response['status']['error_code'] ?? -1) !== 0) {
                            throw new \RuntimeException('Market provider unavailable');
                        }
                        $quotes = [];
                        foreach (self::ASSETS as $id => $symbol) {
                            $asset = $response['data'][$id] ?? [];
                            $quote = $asset['quote']['USD'] ?? [];
                            if (($asset['symbol'] ?? '') !== $symbol || ! is_numeric($quote['price'] ?? null)
                                || $quote['price'] <= 0 || ! is_numeric($quote['percent_change_24h'] ?? null)
                                || ! preg_match('/^[a-z0-9-]+$/', $asset['slug'] ?? '')
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
                                'source' => 'https://coinmarketcap.com/currencies/'.$asset['slug'].'/',
                            ];
                        }
                        $cache->forever('market.cmc.v2.quotes', $quotes);
                    }
                } catch (\Throwable $error) {
                    // Preserve verified data. Manual/demo prices must never substitute for failed API quotes.
                } finally {
                    $lock->release();
                }
            }
        }
        return collect($cache->get('market.cmc.v2.quotes', []))->map(function ($quote) {
            $quote['stale'] = Carbon::parse($quote['captured_at'])->lt(now()->subMinutes(15));
            return $quote;
        });
    }
}
