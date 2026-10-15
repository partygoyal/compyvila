# CoinMarketCap prices

Set `COINMARKETCAP_API_KEY` in the private hosting environment and rebuild Laravel's config cache. Never commit credentials.

Public home, Bitcoin, Ethereum, and market notes use one batch request for CoinMarketCap IDs 1, 1027, 5426, and 1839 (BTC, ETH, SOL, BNB), converted to USD. A shared file cache and lock bound refresh attempts to once per five minutes across visitors. Refresh is demand driven: no visitors means no API requests. A continuously visited 31-day month requires at most 8,928 regular batch requests; measured verification requests are additional. The verified endpoint charged one credit per four-coin batch.

Provider observation timestamps appear in IST. Quotes older than 15 minutes display “Delayed”. A failed or incomplete batch preserves the last verified batch and backs off five minutes. With no successful batch, prices display as unavailable; illustrative database prices never substitute for provider data while the key is configured. Disabling the key restores the labelled manual snapshot workflow.

The API key remains in the private environment/config cache, never in rendered markup or browser JavaScript. Requests are server to server. Manual snapshot records are preserved for editors but do not override provider quotes.

References: https://coinmarketcap.com/api/pricing/ and https://coinmarketcap.com/api/documentation/pro-api-reference/cryptocurrency
