@if(session('flash'))
    <div class="flash" role="status">{{ session('flash') }}</div>
@endif
@if($errors->any())
    <div class="flash" role="alert">
        <ul>@foreach($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul>
    </div>
@endif
