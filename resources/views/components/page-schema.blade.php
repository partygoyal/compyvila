@isset($pageSchema)
<meta name="robots" content="index, follow, max-image-preview:large">
<script type="application/ld+json">{!! json_encode($pageSchema, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) !!}</script>
@endisset
