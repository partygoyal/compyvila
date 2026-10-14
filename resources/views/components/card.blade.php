
<article class="story-card" data-saved-slug="{{ $a['slug'] }}"><a href="/read/{{ $a['slug'] }}" class="story-visual" tabindex="-1" aria-hidden="true">@if($a['image'])<img src="{{ $a['image'] }}" alt="" loading="lazy">@else
@include('components.art', ['coin' => $a['coin'], 'large' => false])
@endif</a><div class="story-meta"><span class="tag">{{ $a['category'] }}</span><span>{{ human_date($a['published_at']) }}</span></div><h3><a href="/read/{{ $a['slug'] }}">{{ $a['title'] }}</a></h3><p>{{ $a['excerpt'] }}</p><div class="story-bottom"><span>{{ max(2,(int)ceil(str_word_count($a['body'])/180)) }} MIN READ</span><button class="save-button" data-save="{{ $a['slug'] }}" data-title="{{ $a['title'] }}" aria-label="Save {{ $a['title'] }}" aria-pressed="false">＋ Save</button></div></article>
