# Final Compyvila logo

The charcoal tile, ivory C and orange four-point signal spark are the brand symbol. The lowercase outlined wordmark and orange period form the full logo. The wordmark uses fixed vector paths rather than a browser font, so it renders consistently on different devices.

Single master: `public/assets/brand/compyvila-master.svg`.

Build: `node scripts/build-brand.mjs` on a workstation with Chrome and PHP. `php scripts/build-icons.php` delegates to the same build. This exports dark/light SVG and transparent PNG logos, favicon SVG/PNG/ICO, Apple touch icon, app icons, and the default social image. All raster icons are rendered from the exact same SVG geometry. Legacy logo/icon endpoints are aliases of the current design.

Public and admin headers/footers use `components.brand`. The Creator Network artwork uses the light variant. Media-kit downloads and the printable kit use the same logo. Article cover generation reads the canonical PNG; the October 15/16 news and company-profile covers have been rebuilt with it. The AI series artwork and Creator Network banner have also been updated to the new logo; versioned image URLs keep cached old artwork out of active article previews.

Fresh URLs for the SVG mark, full logo, social banners, manifest, and Apple/ICO references address browser caches. Default sharing artwork is `assets/social/compyvila-v4.jpg`. Creator Network sharing artwork is `assets/social/creator-network-v2.jpg`, edited with built-in imagegen to incorporate the new logo while preserving its original composition.

Palette: charcoal #20231F, orange #F15A24, ivory #F8F7F2. Keep logo proportions intact and allow clear space around the symbol. Do not recreate the mark using font characters or independent CSS shapes.

Built-in imagegen edit prompts: [Creator Network](creator-network-brand-v3-edit.json) and [AI series](ai-series-brand-v3-edits.json). The logo itself is repo-native outlined vector artwork; generated edits only update the logo within existing raster illustrations.
