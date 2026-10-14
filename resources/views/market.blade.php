@extends('layouts.public')
@section('content')
<section class="page-intro"><span class="eyebrow">MANUAL MARKET NOTES</span><h1>{{ $title }}</h1><p>These are manually entered observations, not live quotes. Check each timestamp and source before using a figure. Changes describe the 24-hour period at the time of observation.</p></section>
<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>ASSET</th><th>PRICE (USD)</th><th>24H CHANGE</th><th>OBSERVED AT (IST)</th><th>SOURCE</th></tr></thead><tbody>@foreach($snapshots as $snap)<tr><td>{{ $snap['name'].' / '.$snap['symbol'] }}</td><td>${{ number_format((float)$snap['price'],2) }}</td><td>{{ number_format((float)$snap['change_pct'],2) }}%</td><td>{{ $snap['captured_at'] }}</td><td>@if(safe_url($snap['source']))<a href="{{ $snap['source'] }}" target="_blank" rel="noopener noreferrer">{{ parse_url($snap['source'],PHP_URL_HOST) }} ↗</a>@else{{ $demo ? 'Illustrative sample, not an observed quote' : 'Source not supplied' }}@endif</td></tr>@endforeach</tbody></table></div>

@endsection
