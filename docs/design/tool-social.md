# Free Crypto Tools and Crypto Quiz social banners

Thirteen distinct 1200 × 630 JPEGs cover all eleven tools, the tools directory, and Crypto Quiz. Each has a large title, two lines explaining its purpose, the canonical Compyvila logo and a topic-specific diagram. Figures are labelled as illustrative examples. Quiz art describes ten questions and explanations with no timer or wallet requirement.

Assets: `public/assets/social/tools/*-v1.jpg`. Editable native SVG sources: `resources/design/tool-social/*.svg`. Metadata and exact byte counts: `resources/data/tool-social.json`.

Rebuild with `node scripts/build-tool-social.mjs` using installed Chrome. This is native vector design and precise typesetting, not AI raster generation. The renderer exports JPEGs at 1200 × 630, lowering quality only when necessary to stay under 100,000 bytes. No remote images, fonts or APIs are required. Inspect the resulting graphics after changing copy or layout. Change the filename version when replacing an already published asset to avoid stale image caches.

`ToolSeo::data()` supplies each page's absolute `socialPreview` URL and descriptive alt text to the shared social metadata component. Open Graph, Twitter and page structured data reference the same JPEG. Existing article/home/creator previews are unaffected.

`ToolSocialTest` verifies unique images, byte limits, dimensions, JPEG MIME, page mapping and metadata consistency for all thirteen pages. A normal HTTP response or bot-user-agent fetch verifies public delivery but does not guarantee that every platform has refreshed a previously cached page preview.
