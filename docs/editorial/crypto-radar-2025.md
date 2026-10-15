# Crypto Radar: first 2025 collection

Researched October 15, 2026. Six listings describe 2025 launch milestones, not projects necessarily founded that year. Current stage is an editorial classification based on accessible production documentation, not a live uptime indicator. No runtime external API is added.

| Listing | Recorded milestone | Evidence |
| --- | --- | --- |
| Unichain | Mainnet, February 11, 2025 | Uniswap Labs launch announcement and official network reference |
| Berachain | Mainnet, February 6, 2025 | Kraken BERA disclosure whitepaper D.8, plus current Berachain introduction/token docs |
| Walrus | Public mainnet announcement, March 27, 2025 | Official announcement distinguishes this from epoch 1 starting March 25 |
| Monad | Mainnet, November 24, 2025 | Current Monad introduction and contemporaneous Uniswap integration announcement |
| Initia | Mainnet, 2025; exact date omitted | April 26, 2025 governance record states mainnet was already live a few days earlier |
| Uniswap v4 | Product launch, January 31, 2025 | Official release announcement and current protocol overview |

The source URL manifest is `content/projects-2025.json`; original profile bodies are `content/projects/*.md`. Each profile includes an overview, milestone evidence, network/token context, research considerations, and related Compyvila guides. The directory additionally shows category, stage, and network; profiles show dates where established, source URLs, official website, review date, and related listings where available.

Unichain and Uniswap v4 token fields are deliberately blank: ETH gas and an existing governance token must not be presented as a new token launched with this network or protocol release. Initia's exact launch day is not inferred from the governance-post date. No price, market cap, TVL, funding round, return forecast, audit guarantee, or airdrop eligibility is invented or displayed.

Validate with `php artisan compyvila:publish-projects`. Publish missing slugs with `--publish`. The importer validates the entire batch before inserting in a transaction. Existing slugs, corrections, publication states, and review timestamps survive reimports. Subsequent edits belong in the editorial desk. A repository content update alone does not overwrite a published listing.
