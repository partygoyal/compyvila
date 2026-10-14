@php $coin = $coin ?? 'Bitcoin'; $large = $large ?? false; @endphp

<div class="coin-art {{ strtolower($coin) }} {{ $large ? 'large-art' : '' }}" aria-hidden="true"><div class="art-grid"></div><div class="orbit orbit-one"></div><div class="orbit orbit-two"></div><span class="art-coordinate">PERSPECTIVE / DIGITAL ASSETS</span><span class="art-star star-one">✳</span><span class="art-star star-two">+</span><div class="coin-disc"><span>{{ $coin==='Bitcoin' ? '₿' : ($coin==='Ethereum' ? '♦' : '↗') }}</span></div><span class="art-bottom">A CLEARER VIEW <span>↗</span></span></div>
