# Compyvila free tools

`/tools` lists 11 calculators, converters and the creator campaign planner. The quiz is separate at `/quiz`, with a highlighted navbar link. Its former URL `/tools/crypto-knowledge-quiz` permanently redirects to `/quiz`. All have individual, indexable Laravel pages with canonical/social metadata and sitemap entries. The shared header and footer link to the directory.

Catalog, field definitions, examples and assumptions: `resources/data/tools.json`. `ToolController` only reads this catalog; there are no submission routes, database changes or API dependencies. `public/assets/tools-core.mjs` contains pure calculations, exact BigInt unit conversion and UTC calendar date handling. `tools.js` renders results using DOM text nodes; user labels never become HTML. Inputs are not persisted or submitted. The input fieldset starts disabled until the calculator's submit handler is installed. Existing site analytics still applies to page views.

Money and projections use JavaScript numbers, displaying up to 12 significant digits; these are educational estimates, not accounting records. BTC/ETH converters use exact decimal parsing and integer arithmetic, reject sub-atomic amounts, and return full precision. Numeric inputs reject blanks, negatives, non-finite and excessive values. Editable tables allow 1–50 rows.

Vesting is continuous after the cliff, with an optional initial release at the start and no cliff catch-up. Duration includes the cliff. Months clamp to the destination month's final date; calculations use elapsed UTC time. These assumptions are displayed beside the tool. Staking uses a constant hypothetical APR and interpolates fractional compound periods. Neither is a protocol quote.

Creator quotes apply the rights uplift to the deliverables subtotal and commission to fees including rights and exclusivity, excluding reimbursed expenses. Estimated payout also deducts those production expenses. Rates come from the user.

Validation:

- `node --test tests/tools-calculations.test.mjs`
- `php artisan test --compact`
- `PREVIEW_URL=http://127.0.0.1:8081 node tests/tools-browser-check.mjs` (installed Chrome; start an isolated SQLite preview first). Checks all pages at 1440, 1280, 1200, 1024, 768, 390 and 320 pixels, directory filters, calculator interactions, exact wei conversion and all quiz answers. Writes ignored `storage/qa-tools-*.png` screenshots. Stop the preview server afterwards.
