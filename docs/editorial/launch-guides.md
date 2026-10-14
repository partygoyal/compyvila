# Initial sourced guide release

Researched on October 14, 2026. Six original guides, approximately 620–740 words each, with direct answers, primary documentation, original hypothetical calculations, FAQs, and links between related topics. No invented author identities, live quotes, or guaranteed investment outcomes.

## Search research

Ubersuggest authenticated successfully. The owner authorized up to six daily reports; six unique exact keyword subjects were queried. Overview requests omitted location, so these are global figures without a language filter. Cached SERP checks for seed phrases, FDV, and depegging returned US English results. No paid metric refresh or article generation was requested.

| Exact keyword | Reported average monthly searches | SEO difficulty |
| --- | ---: | --- |
| bitcoin halving explained | 0 | unavailable |
| seed phrase vs private key | 90 | unavailable |
| market cap vs fdv | 20 | unavailable |
| crypto liquidity vs volume | 0 | unavailable |
| ethereum gas fees explained | 0 | unavailable |
| stablecoin depeg | 110 | unavailable |

Zero or missing data does not demonstrate a lack of demand for all related queries. No low-competition claim is justified by unavailable difficulty. Seed phrases and depegging have the strongest measured demand among these exact phrases. The other guides support related educational questions and a connected learning collection. Search and AI citations are not guaranteed.

## Editorial approach

Each guide addresses a distinct definition or comparison, follows with mechanism and an original example, identifies common inference errors, and answers short follow-up questions. Bitcoin developer documentation, BIP specifications, Ethereum documentation and EIP-1559, CoinGecko methodology, Uniswap documentation, Kraken order documentation, Circle issuer documentation, and BIS research support the relevant claims. Source URLs are stored in the manifest and linked near claims in the body.

No current market prices, projected returns, or fixed future halving date are asserted. Issuer redemption claims are distinguished from retail trading access. Supply metrics are distinguished from cash invested, and historical activity is distinguished from present execution capacity.

## Publishing and maintenance

Article metadata and source URLs: `content/guides.json`. Bodies: `content/guides/*.md`.

Run `php artisan compyvila:publish-guides` to validate without changing the database. Run with `--publish` to insert missing slugs in one transaction. Existing slugs are preserved, including edited bodies, drafts, and timestamps; rerunning a deployment does not republish an editor's draft or overwrite corrections. New publications use the actual import time in IST.

The public renderer supports escaped Markdown, tables, lists, and links; raw HTML and unsafe URL schemes cannot execute. Article and breadcrumb structured data describe visible content, publisher, source citations, and actual publication/update timestamps. Sitemap entries include article modification times. Robots excludes administrative and personal utility routes.

Update published guides through the editorial desk when sources or network behavior change. Repository originals serve as a reviewed launch record; subsequent file changes deliberately do not overwrite database edits. All market snapshots remain illustrative and are explicitly labelled.
