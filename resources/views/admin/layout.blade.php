<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>Editorial desk — Compyvila</title>
@include('components.favicons')
<link rel="stylesheet" href="/assets/style.css?v={{ filemtime(public_path('assets/style.css')) }}">@if(request()->is('admin/creators*'))<link rel="stylesheet" href="/assets/creators.css?v={{ filemtime(public_path('assets/creators.css')) }}">@endif<script src="/assets/app.js" defer></script></head><body><header class="admin-header"><div class="container">@include('components.brand')<div class="admin-nav"><span>EDITORIAL DESK</span><a href="/">View website ↗</a>@if($logged)<form method="post" action="/admin/logout">@csrf<input type="hidden" name="action" value="logout"><button class="save-button">Sign out</button></form>@endif</div></div></header><main class="container admin-main">
@include('components.messages')

@if($logged)
<nav class="admin-tabs" aria-label="Editorial sections">@foreach(['overview'=>'Overview','articles'=>'Articles','projects'=>'Crypto Radar','market'=>'Market snapshots','events'=>'Calendar','subscribers'=>'Subscribers','enquiries'=>'Enquiries','settings'=>'Settings'] as $key=>$label)<a class="{{ $tab===$key && $path==='/admin' ? 'active' : '' }}" href="/admin?tab={{ $key }}">{{ $label }}</a>@endforeach<a class="{{ request()->is('admin/creators*') ? 'active' : '' }}" href="/admin/creators">Creator leads</a></nav>
@endif
@yield('content')
</main><div class="toast" role="status" aria-live="polite"></div></body></html>