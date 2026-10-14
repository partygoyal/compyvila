@extends('admin.layout')
@section('content')
<section class="admin-login"><span class="eyebrow">A CLEARER VIEW STARTS HERE</span><h1>The editorial desk.</h1><p>Sign in to manage the publication.</p><form method="post" action="/admin/login" class="admin-form">@csrf<label>Email<input type="email" name="email" autocomplete="username" required></label><label>Password<input type="password" name="password" autocomplete="current-password" required></label><button class="button dark">Sign in ↗</button></form></section>

@endsection