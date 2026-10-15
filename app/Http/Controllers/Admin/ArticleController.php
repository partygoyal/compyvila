<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    public function edit(Request $request)
    {
        $article = $request->filled('id') ? Article::findOrFail($request->integer('id')) : new Article;

        return view('admin.article', ['edit' => $article->toArray()]);
    }

    public function store(Request $request)
    {
        $article = $request->integer('id') ? Article::findOrFail($request->integer('id')) : new Article;
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('title', ''))]);
        $data = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'slug' => ['required', 'string', 'max:190', Rule::unique('articles', 'slug')->ignore($article->id)],
            'excerpt' => ['required', 'string', 'min:10', 'max:2000'],
            'body' => ['required', 'string', 'min:30', 'max:60000'],
            'category' => ['required', Rule::in(['News', 'Analysis', 'Learn'])],
            'coin' => ['required', Rule::in(['Bitcoin', 'Ethereum', 'Altcoins'])],
            'sources' => ['required_if:status,published', 'nullable', 'string', function ($attribute, $value, $fail) use ($request) {
                if ($request->input('status') === 'published' && ! trim($value ?? '')) {
                    $fail('Published stories need at least one source link.');
                }
                foreach (preg_split('/\R/', $value ?? '') as $url) {
                    if (trim($url) !== '' && ! safe_url(trim($url))) {
                        $fail('Each source must be a complete http or https URL, one per line.');
                        break;
                    }
                }
            }],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'featured' => ['nullable', Rule::in(['1', 'on'])],
            'published_at' => ['required', 'date'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=10000,max_height=10000'],
        ]);
        $oldImage = $article->image;
        $image = $request->boolean('remove_image') ? '' : ($oldImage ?? '');
        if ($request->hasFile('image')) {
            $image = '/uploads/'.$request->file('image')->store('', 'editorial');
        }
        unset($data['image']);
        $data['image'] = $image;
        $data['sources'] ??= '';
        $data['featured'] = $request->boolean('featured');
        $data['published_at'] = date('Y-m-d H:i:s', strtotime($data['published_at']));
        $data['updated_at'] = now()->format('Y-m-d H:i:s');
        try {
            $article->fill($data)->save();
        } catch (\Throwable $exception) {
            if ($image && $image !== $oldImage) {
                Storage::disk('editorial')->delete(basename($image));
            }
            throw $exception;
        }
        if ($oldImage && str_starts_with($oldImage, '/uploads/') && $oldImage !== $image) {
            Storage::disk('editorial')->delete(basename($oldImage));
        }

        return redirect('/admin?tab=articles')->with('flash', 'Article saved. Future publication dates are released automatically when the date arrives.');
    }
}
