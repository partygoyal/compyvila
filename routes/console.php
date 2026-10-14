<?php
use App\Models\Admin;
use Illuminate\Support\Facades\Artisan;
use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
