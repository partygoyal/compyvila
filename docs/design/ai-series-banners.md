# AI × crypto article banners

Generated with the built-in imagegen tool, inspected for spelling, composition, and topic fit, then exported to WebP at quality 90 with PHP GD. Original generated PNGs remain in the generated-image library. No visual crop or compositing was applied to the saved derivatives.

Final prompts: [ai-series-banners.json](ai-series-banners.json).

| Banner | Saved project asset | Palette |
| --- | --- | --- |
| AI AGENTS | `public/assets/articles/ai-agents-crypto-wallets.webp` | Coral, cobalt, cream |
| DECENTRALIZED AI | `public/assets/articles/bittensor-decentralized-ai.webp` | Violet, pink, turquoise |
| THE COMPUTE LAYER | `public/assets/articles/akash-ai-gpu-compute.webp` | Teal, amber, indigo |

All three are landscape images of approximately 1.9:1. Article pages preserve their natural aspect ratio. Homepage and listing frames contain the full artwork so generated lettering remains visible. Article social metadata and Article structured data use the corresponding banner; pages without an article image retain the Compyvila brand cover. Open Graph dimensions are omitted because the hosting image optimizer can serve resized derivatives.

These are conceptual editorial illustrations, not screenshots, architecture diagrams, benchmark results, or endorsements by the platforms discussed.

## Social preview compatibility update

Website banners remain full-quality WebP. Sharing metadata now uses separate 1200×630 JPEG exports under `public/assets/social/`, all under 150 KB for this series. Images are contained without cropping, with a warm ivory background where padding is needed. Content-hashed filenames version the three article exports; `compyvila-v3.jpg` is the lightweight brand fallback. Metadata declares the actual JPEG type and dimensions.

`App\Support\SocialPreview` also creates JPEG previews for future local article uploads when GD can decode the image. These go under persistent `public/uploads/social/` and use content hashes so an updated upload gets a different preview URL. Unavailable, oversized, unsupported, or nonlocal source images fall back to the prepared brand JPEG. No external URL is fetched. Existing original images are retained.
