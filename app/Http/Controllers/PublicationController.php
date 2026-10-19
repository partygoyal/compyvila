<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Event;
use App\Models\Snapshot;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicationController extends Controller
{
    private const TITLES = [
        '/quiz' => 'Crypto Knowledge Quiz', '/tools' => 'Free Crypto Tools & Calculators', '/creator-network' => 'Join the Compyvila Creator Network', '/' => 'Crypto, with perspective.', '/news' => 'The latest, with context.',
        '/analysis' => 'Look beyond the price.', '/bitcoin' => 'The original. Still evolving.',
        '/ethereum' => 'The programmable frontier.', '/altcoins' => 'Explore the wider ecosystem.',
        '/learn' => 'Understanding starts here.', '/calendar' => 'Know what’s ahead.',
        '/media-kit' => 'Let’s make an impression.', '/market-notes' => 'A snapshot, with context.',
        '/search' => 'Find your next insight.', '/watchlist' => 'Your reading list.',
        '/editorial-policy' => 'Our editorial standards.', '/privacy' => 'Your privacy matters.',
        '/terms' => 'Terms of use.', '/contact' => 'Let’s talk.',
    ];

    private function render(string $view, array $data = []): View
    {
        $path = '/'.trim(request()->path(), '/');

        return view($view, array_merge([
            'path' => $path,
            'title' => self::TITLES[$path] ?? 'Page not found',
            'description' => 'Independent perspective on digital assets. Crypto news, thoughtful analysis, practical guides, and a clearer view of the market.',
            'article' => null,
            'demo' => setting('demo', '1') === '1',
        ], $data));
    }

    public function home(): View
    {
        return $this->render('home', [
            'heroArticles' => Article::published()->latest('published_at')->orderByDesc('id')->take(10)->get(),
            'feature' => Article::published()->orderByDesc('featured')->latest('published_at')->first(),
            'snapshots' => app(\App\Services\MarketPrices::class)->quotes(),
            'dispatches' => Article::published()->latest('published_at')->skip(1)->take(4)->get(),
            'analyses' => Article::published()->where('category', 'Analysis')->latest('published_at')->take(3)->get(),
            'events' => Event::where('event_at', '>=', today()->format('Y-m-d H:i:s'))->orderBy('event_at')->take(3)->get(),
            'guides' => Article::published()->where('category', 'Learn')->latest('published_at')->take(3)->get(),
        ]);
    }

    public function listing(Request $request): View
    {
        $path = '/'.$request->path();
        $q = trim(mb_substr((string) $request->query('q', ''), 0, 200));
        $filter = (string) $request->query('category', '');
        $query = Article::published();
        if (in_array($path, ['/news', '/analysis', '/learn'])) {
            $query->where('category', ucfirst(substr($path, 1)));
        }
        if (in_array($path, ['/bitcoin', '/ethereum', '/altcoins'])) {
            $query->where('coin', ucfirst(substr($path, 1)));
        }
        if ($q !== '') {
            $query->where(fn ($query) => $query->where('title', 'like', '%'.$q.'%')
                ->orWhere('excerpt', 'like', '%'.$q.'%')->orWhere('body', 'like', '%'.$q.'%'));
        }
        if (in_array($filter, ['News', 'Analysis', 'Learn'])) {
            $query->where('category', $filter);
        }
        $paginator = $query->latest('published_at')->paginate(9)->withQueryString();
        $coin = $path === '/bitcoin' ? 'BTC' : 'ETH';

        return $this->render('listing', [
            'q' => $q, 'filter' => $filter,
            'items' => $paginator->items(), 'total' => $paginator->total(),
            'page' => $paginator->currentPage(), 'perPage' => $paginator->perPage(),
            'savedArticles' => $path === '/watchlist' ? Article::published()->latest('published_at')->get() : collect(),
            'coin' => $coin, 'snap' => in_array($path, ['/bitcoin', '/ethereum']) ? app(\App\Services\MarketPrices::class)->quotes()->firstWhere('symbol', $coin) : null,
        ]);
    }

    public function article(string $slug): View
    {
        $article = Article::published()->where('slug', $slug)->firstOrFail();

        return $this->render('article', [
            'article' => $article, 'title' => $article->title, 'description' => $article->excerpt,
            'related' => Article::published()->where('id', '<>', $article->id)->latest('published_at')->take(3)->get(),
        ]);
    }

    public function calendar(Request $request): View
    {
        $category = (string) $request->query('category', '');
        $when = $request->query('when') === 'past' ? 'past' : 'upcoming';
        $query = Event::where('event_at', $when === 'past' ? '<' : '>=', today()->format('Y-m-d H:i:s'));
        if (in_array($category, ['Editorial', 'Learn', 'Upgrade', 'Unlock', 'Economic', 'Conference'])) {
            $query->where('category', $category);
        }

        return $this->render('calendar', [
            'category' => $category, 'when' => $when,
            'events' => $query->orderBy('event_at', $when === 'past' ? 'desc' : 'asc')->get(),
        ]);
    }

    public function page(): View
    {
        $page = request()->path();
        $template = match ($page) {
            'media-kit' => 'media', 'market-notes' => 'market', default => 'policies',
        };

        return $this->render($template, ['snapshots' => $page === 'market-notes' ? app(\App\Services\MarketPrices::class)->quotes() : collect()]);
    }

    public function downloadKit()
    {
        return response()->view('kit')->header('Content-Disposition', 'attachment; filename="compyvila-media-kit.html"');
    }

    public function sitemap()
    {
        $urls = collect(array_keys(self::TITLES))->reject(fn ($path) => in_array($path, ['/search', '/watchlist']))
            ->map(fn ($path) => ['path' => $path, 'modified' => null])->values()->all();
        foreach (\App\Support\ToolCatalog::all() as $tool) {
            $urls[] = ['path' => '/tools/'.$tool['slug'], 'modified' => null];
        }
        foreach (Article::published()->get(['slug', 'updated_at']) as $article) {
            $urls[] = ['path' => '/read/'.$article->slug, 'modified' => \Illuminate\Support\Carbon::parse($article->updated_at)->toAtomString()];
        }
        $urls[] = ['path' => '/projects', 'modified' => null];
        foreach (\App\Models\Project::published()->get(['slug', 'updated_at']) as $project) {
            $urls[] = ['path' => '/projects/'.$project->slug, 'modified' => $project->updated_at->toAtomString()];
        }

        return response()->view('sitemap', ['urls' => $urls])->header('Content-Type', 'application/xml; charset=utf-8');
    }

    public function robots()
    {
        return response("User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /search\nDisallow: /watchlist\nSitemap: ".rtrim(config('app.url'), '/')."/sitemap.xml\n")
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }
}
