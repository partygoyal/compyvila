@extends('layouts.public')
@section('content')

<section class="page-intro"><span class="eyebrow">THE CRYPTO CALENDAR</span><h1>{{ $title }}</h1><p>The dates worth a closer look. Curated manually, with sources and verification dates.<br>All times are in India Standard Time (IST).</p></section>
<form class="filter-bar" method="get"><select name="when" aria-label="Event timeframe"><option value="upcoming">Upcoming events</option><option value="past" {{ $when==='past' ? 'selected' : '' }}>Past events</option></select><select name="category" aria-label="Event category"><option value="">All event types</option>@foreach(['Editorial','Learn','Upgrade','Unlock','Economic','Conference'] as $c)<option {{ $category===$c ? 'selected' : '' }}>{{ $c }}</option>@endforeach</select><button class="button dark small">Apply filters ↗</button></form>
<div class="calendar-list">@foreach($events as $event)<article id="event-{{ $event['id'] }}" class="calendar-event"><div class="date-tile"><span>{{ date('M',strtotime($event['event_at'])) }}</span><b>{{ date('d',strtotime($event['event_at'])) }}</b><span>{{ date('Y',strtotime($event['event_at'])) }}</span></div><div><span class="eyebrow">{{ $event['category'] }} / {{ date('H:i',strtotime($event['event_at'])) }} IST</span><h2>{{ $event['title'] }}</h2><p>{{ $event['description'] }}</p><small>Verified {{ human_date($event['verified_at']) }}</small></div>@if(safe_url($event['source']))<a class="text-link" href="{{ $event['source'] }}" target="_blank" rel="noopener noreferrer">Original source ↗</a>@endif</article>@endforeach
@if($events->isEmpty())<div class="empty-state"><h2>No events in this view.</h2><p>Check another category or timeframe.</p></div>@endif</div>

@endsection
