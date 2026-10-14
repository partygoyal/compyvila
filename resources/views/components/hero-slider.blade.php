<section class="blog-hero" aria-label="Latest blog posts" aria-roledescription="carousel" data-blog-slider>
    <div class="hero-topline">
        <span class="eyebrow"><span class="orange-line"></span> THE LATEST PERSPECTIVES</span>
        <span class="eyebrow muted">CRYPTO, WITH PERSPECTIVE.</span>
    </div>
    @if($heroArticles->isNotEmpty())<h1 class="sr-only">Latest crypto stories and perspectives</h1>@endif
    @forelse($heroArticles as $post)
        <article class="hero-slide" data-hero-slide role="group" aria-roledescription="slide" aria-label="{{ $loop->iteration }} of {{ $heroArticles->count() }}" @if(!$loop->first) hidden @endif>
            <div class="hero-story-copy">
                <div class="story-meta"><span class="tag">{{ $post->category }}</span><span>{{ human_date($post->published_at) }} / {{ $post->coin }}</span></div>
                <h2><a href="{{ route('articles.show', $post->slug) }}">{{ $post->title }}</a></h2>
                <p>{{ $post->excerpt }}</p>
                <a class="button dark" href="{{ route('articles.show', $post->slug) }}">Read the story <span>↗</span></a>
            </div>
            <a class="hero-story-art" href="{{ route('articles.show', $post->slug) }}" tabindex="-1" aria-hidden="true">
                @if($post->image)
                    <img src="{{ $post->image }}" alt="" @if($loop->first) fetchpriority="high" @else loading="lazy" @endif>
                @else
                    @include('components.art', ['coin' => $post->coin, 'large' => true])
                @endif
            </a>
        </article>
    @empty
        <div class="hero-empty"><h1>Crypto, with perspective.</h1><p>Fresh stories are on their way. Explore our learning library in the meantime.</p><a class="button dark" href="/learn">Explore the library ↗</a></div>
    @endforelse
    @if($heroArticles->isNotEmpty())
        <div class="hero-controls">
            <span class="hero-counter" data-hero-counter aria-live="off" aria-atomic="true">01 <span>/ {{ str_pad((string)$heroArticles->count(), 2, '0', STR_PAD_LEFT) }}</span></span>
            @if($heroArticles->count() > 1)
                <div class="hero-dots" role="group" aria-label="Choose a blog post">
                    @foreach($heroArticles as $post)
                        <button type="button" class="hero-dot" data-hero-dot="{{ $loop->index }}" aria-label="Show post {{ $loop->iteration }}: {{ $post->title }}" aria-current="{{ $loop->first ? 'true' : 'false' }}"></button>
                    @endforeach
                </div>
                <div class="hero-buttons">
                    <button type="button" class="hero-play" data-hero-play aria-label="Pause slideshow">Pause <span aria-hidden="true">Ⅱ</span></button>
                    <button type="button" class="hero-arrow" data-hero-prev aria-label="Previous blog post">←</button>
                    <button type="button" class="hero-arrow" data-hero-next aria-label="Next blog post">→</button>
                </div>
            @endif
        </div>
    @endif
</section>
