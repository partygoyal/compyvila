# Crypto Funding tracker

Public: `/funding`. Detail pages: `/funding/{round-slug}`. Admin: `/admin/funding` (existing editorial login).

The initial collection contains **23 historical announcements across 18 startups**, spanning 2025–2026. Company and investor announcements and company-issued press releases are preferred. The collection is curated and is not a complete market database. Private businesses at later stages are included alongside early-stage startups; the directory does not impose a founding-year cutoff.

`content/funding-2025-2026.json` contains the source-linked startup and round records. `php artisan compyvila:publish-funding` validates only. Add `--publish` to insert missing profiles and rounds in one transaction. Existing drafts, edits, notes, statuses and review dates are preserved. Publishing this collection does not create submissions or contact any third parties. No API, scraper or automatic daily refresh is used.

## Data conventions

- Dates are announcement dates, not company launches or assumed closing dates. Public rounds must have an announcement date no later than today.
- Money fields are positive USD amounts or null. Zero is rejected. Undisclosed values are displayed as such and excluded from sums.
- Venture financing and other financing are summed separately; private/public token sales, grants and debt have their own financing types. “Venture” describes the funding context, not an assertion that an unspecified instrument was equity.
- Every figure is the amount for one round. The Privy and Multiliquid entries leave this amount undisclosed because the cited primary announcements provide cumulative funding but not a standalone round amount. Multiliquid's $8M cumulative headline is not counted as a new $8M round.
- Valuation is separate from funding, with an explicit pre-money, post-money, token FDV or unspecified basis. Valuations are not guessed.
- Startup profiles are separate from rounds. A profile can link to an existing Crypto Radar project; a public link is shown only when that project is published. The Walrus entry links automatically when its profile already exists.
- Summary cards and totals reflect the current filters. Company history includes only published tracked rounds, not necessarily lifetime funding.

## Editorial workflow

1. Add or edit a startup profile, then add a round with a unique slug, announcement date, financing type and original sources.
2. Keep investor names one per line, and separate lead and other participants. Leave unknown lead names blank.
3. Use public source context to explain cumulative figures, unclear round labels, regional release dates or secondary reporting. Private editorial notes never render publicly.
4. Every published save requires the editor to check the review checkbox. That records the review timestamp. Source review is not an audit of the transaction.
5. To remove coverage, save as draft or delete the round with explicit confirmation. The startup remains available for its other rounds.

The collection's Rain March 2025 record uses the industry association's member profile for the Series A classification and contemporaneous reporting for the exact date. Walrus is a private token sale: investor Raptor's release is dated March 21, while contemporaneous reporting places the announcement on March 20. The Project Eleven source is the January 14 distribution; other regional distributions appeared January 15. No valuations are inferred from unsourced market estimates.

## Founder submissions

`POST /funding/submit` validates consent, HTTP(S) source and website links, field lengths and a honeypot, with a five-per-minute limit. Links are stored and never fetched by the server. Submissions go only into the private funding inbox and cannot publish records or set their own review status or notes. Editors can review, annotate or delete submissions. There are no automatic emails. Privacy text describes the contact-data use and deletion route.

## Validation and assets

`FundingTest` covers publication visibility, filtering and separated totals, repeat publishing, authentication, validation, review requirements, source safety, CRUD and private submissions. Browser QA checked five widths (320–1440px), filters, detail pages, admin screens and a complete test submission/review/deletion in isolated SQLite.

The compact funding OG JPEG is `public/assets/social/funding-v1.jpg` (1200×630). Native generator: `php scripts/build-funding-cover.php [bold-ttf-path]`. Both directory and detail pages use this image, with canonical URLs and detail-page WebPage/BreadcrumbList structured data. Public round URLs are included in the sitemap.

Keep the tracker updated through editorial review. No future daily research or automatic publication is scheduled by this implementation.
