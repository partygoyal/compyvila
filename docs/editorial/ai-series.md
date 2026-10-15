# AI × crypto educational series

Researched October 15, 2026, using primary documentation. Three original articles of approximately 800–850 words each, with distinct creative covers, direct explanations, role tables, hypothetical examples, practical evaluation questions, FAQs, and internal links.

- Coinbase AgentKit: wallet providers, action providers, and the distinction between agent planning, permission enforcement, wallet signing, and chain execution. The separate Agentic Wallet CLI skills are explicitly identified as a different integration. Budget examples are hypothetical, not platform limits or prices.
- Bittensor: specialized subnets, service production, validator evaluation, and token incentives. Network participation or rewards do not establish model accuracy or customer demand. Privacy and scoring limitations are identified without repeating volatile staking thresholds or promised returns.
- Akash: GPU workloads, provider bids, lease coordination, offchain execution, and operations. No live hardware prices, comparative savings claims, or benchmarks are asserted. Cost examples are invented arithmetic, and proposed tests are not represented as tests performed by Compyvila.

Source URLs are stored in `content/ai-series.json`, with citations next to the relevant discussion in `content/ai-series/*.md`. No additional Ubersuggest quota or paid article generation was used.

Validate with `php artisan compyvila:publish-ai-series`. Publish missing slugs with `--publish`. The command verifies manifest fields, source URLs, body limits, and banner presence before inserting the batch in a transaction. Existing edited records and drafts are preserved on reimport. Subsequent editorial maintenance happens in the admin desk.

Banner prompts, saved asset paths, and generation details are recorded in `docs/design/ai-series-banners.md` and its linked JSON prompt set. No external market or AI API is called by the website to render these articles.
