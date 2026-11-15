@php
    $time = \Illuminate\Support\Carbon::parse($commit['date'])->timezone('Asia/Kolkata')->format('h:i A');
@endphp
<small class="text-muted text-nowrap">{{ $time }}</small>
<code>{{ $commit['hash'] }}</code>
@if ($commit['type'])
    <span class="badge {{ $commit['type_badge'] }}">{{ $commit['type'] }}</span>
@endif
<span>{{ $commit['summary'] }}</span>