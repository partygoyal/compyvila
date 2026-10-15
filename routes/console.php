<?php
use App\Models\Admin;
use Illuminate\Support\Facades\Artisan;
use App\Models\Article;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

Artisan::command('compyvila:publish-ai-series {--publish : Insert missing articles; otherwise validate only}', function () {
    $articles = json_decode(file_get_contents(base_path('content/ai-series.json')), true, flags: JSON_THROW_ON_ERROR);
    $records = [];
    foreach ($articles as $article) {
        Validator::make($article, [
            'slug'=>['required','string','regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/','max:190'],
            'title'=>['required','string','max:250'], 'excerpt'=>['required','string','max:1000'],
            'category'=>['required','in:News,Analysis,Learn'], 'coin'=>['required','in:Bitcoin,Ethereum,Altcoins'],
            'sources'=>['required','array','min:1'], 'sources.*'=>['required','url:http,https'],
            'image'=>['required','string'],
        ])->validate();
        if ($article['image'] !== '/assets/articles/'.$article['slug'].'.webp' || ! is_file(public_path(ltrim($article['image'], '/')))) {
            $this->error('Missing or invalid banner for '.$article['slug'].'. No articles published.');
            return 1;
        }
        $body = file_get_contents(base_path('content/ai-series/'.$article['slug'].'.md'));
        Validator::make(['body'=>$body], ['body'=>['required','string','min:30','max:60000']])->validate();
        $records[] = array_merge($article, ['body'=>$body, 'sources'=>implode("\n", $article['sources'])]);
    }
    if (count(array_unique(array_column($records, 'slug'))) !== count($records)) {
        $this->error('Duplicate article slugs. No articles published.');
        return 1;
    }
    if (! $this->option('publish')) {
        $this->info(count($records).' AI articles and banners validated. Use --publish to insert missing articles.');
        return 0;
    }
    DB::transaction(function () use ($records) {
        foreach ($records as $record) {
            $article = Article::firstOrCreate(['slug'=>$record['slug']], array_merge($record, [
                'status'=>'published', 'featured'=>false,
                'published_at'=>now()->format('Y-m-d H:i:s'), 'updated_at'=>now()->format('Y-m-d H:i:s'),
            ]));
            $this->line(($article->wasRecentlyCreated ? 'Published: ' : 'Preserved existing: ').$article->slug);
        }
    });
    return 0;
})->purpose('Publish the sourced AI and crypto series with its creative banners, preserving existing editorial changes');

Artisan::command('compyvila:publish-projects {--publish : Insert missing listings; otherwise validate only}', function () {
    $projects = json_decode(file_get_contents(base_path('content/projects-2025.json')), true, flags: JSON_THROW_ON_ERROR);
    $records = [];
    foreach ($projects as $project) {
        Validator::make($project, [
            'slug' => ['required', 'string', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'max:190'],
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'tagline' => ['required', 'string', 'min:10', 'max:300'],
            'category' => ['required', Illuminate\Validation\Rule::in(Project::CATEGORIES)],
            'stage' => ['required', Illuminate\Validation\Rule::in(Project::STAGES)],
            'milestone' => ['required', Illuminate\Validation\Rule::in(Project::MILESTONES)],
            'network' => ['required', 'string', 'max:120'],
            'symbol' => ['nullable', 'string', 'max:20'],
            'launch_year' => ['required', 'integer', 'in:2025'],
            'launch_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:2025-01-01', 'before_or_equal:2025-12-31'],
            'website' => ['required', 'url:http,https', 'max:500'],
            'sources' => ['required', 'array', 'min:1'],
            'sources.*' => ['required', 'url:http,https'],
        ])->validate();
        $body = file_get_contents(base_path('content/projects/'.$project['slug'].'.md'));
        Validator::make(['description'=>$body], ['description'=>['required', 'string', 'min:30', 'max:30000']])->validate();
        $records[] = array_merge($project, ['description'=>$body, 'sources'=>implode("\n", $project['sources'])]);
    }
    if (count(array_unique(array_column($records, 'slug'))) !== count($records)) {
        $this->error('Duplicate project slugs. No listings published.');
        return 1;
    }
    if (! $this->option('publish')) {
        $this->info(count($records).' projects validated. Use --publish to insert missing listings.');
        return 0;
    }
    DB::transaction(function () use ($records) {
        foreach ($records as $record) {
            $project = Project::firstOrCreate(['slug'=>$record['slug']], array_merge($record, [
                'status'=>'published', 'published_at'=>now(),
            ]));
            $this->line(($project->wasRecentlyCreated ? 'Published: ' : 'Preserved existing: ').$project->slug);
        }
    });
    return 0;
})->purpose('Publish the researched 2025 Crypto Radar collection while preserving existing editorial records');

Artisan::command('compyvila:publish-guides {--publish : Insert missing guides; otherwise validate only}', function () {
    $guides = json_decode(file_get_contents(base_path('content/guides.json')), true, flags: JSON_THROW_ON_ERROR);
    $records = [];
    foreach ($guides as $guide) {
        Validator::make($guide, [
            'slug' => ['required', 'string', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'max:190'],
            'title' => ['required', 'string', 'max:250'],
            'excerpt' => ['required', 'string', 'max:1000'],
            'category' => ['required', 'in:News,Analysis,Learn'],
            'coin' => ['required', 'in:Bitcoin,Ethereum,Altcoins'],
            'sources' => ['required', 'array', 'min:1'],
            'sources.*' => ['required', 'url:http,https'],
        ])->validate();
        $body = file_get_contents(base_path('content/guides/'.$guide['slug'].'.md'));
        Validator::make(['body' => $body], ['body' => ['required', 'string', 'min:30', 'max:60000']])->validate();
        $records[] = array_merge($guide, ['body' => $body, 'sources' => implode("\n", $guide['sources'])]);
    }
    if (count(array_unique(array_column($records, 'slug'))) !== count($records)) {
        $this->error('Duplicate guide slugs. No articles published.');
        return 1;
    }
    if (! $this->option('publish')) {
        $this->info(count($records).' guides validated. Use --publish to insert missing articles.');
        return 0;
    }
    DB::transaction(function () use ($records) {
        foreach ($records as $record) {
            $article = Article::firstOrCreate(['slug' => $record['slug']], array_merge($record, [
                'image' => '', 'featured' => false, 'status' => 'published',
                'published_at' => now()->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s'),
            ]));
            $this->line(($article->wasRecentlyCreated ? 'Published: ' : 'Preserved existing: ').$article->slug);
        }
    });
    return 0;
})->purpose('Validate and publish sourced guides without overwriting existing editorial records');

Artisan::command('compyvila:create-admin {email}', function () {
    $email = strtolower(trim($this->argument('email')));
    if (! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
        $this->error('Enter a valid email address.');
        return 1;
    }
    if (Admin::where('email', $email)->exists()) {
        $this->error('That admin already exists. Existing passwords are not overwritten.');
        return 1;
    }
    $password = bin2hex(random_bytes(12));
    Admin::create(['email' => $email, 'password' => $password]);
    $this->line('Admin created: '.$email);
    $this->line('Password: '.$password);
    $this->info('Save this password in your password manager.');
    return 0;
})->purpose('Create an editorial administrator with a generated password');
