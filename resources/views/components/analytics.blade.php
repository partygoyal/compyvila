@if(request()->attributes->has('analytics.nonce'))
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ config('analytics.measurement_id') }}"></script>
<script nonce="{{ request()->attributes->get('analytics.nonce') }}">
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', {!! json_encode(config('analytics.measurement_id'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}, {
    allow_google_signals: false,
    allow_ad_personalization_signals: false
  });
</script>
@endif
