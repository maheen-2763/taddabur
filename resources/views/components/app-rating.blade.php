@php
    $rating = \App\Support\AppRating::summary();
    $label = $rating
        ? number_format($rating['average'], 1) . ' / 5 from ' . number_format($rating['count']) . ' ' . \Illuminate\Support\Str::plural('rating', $rating['count'])
        : null;
@endphp
@if ($rating)
    <p class="mb-2" style="font-size:0.8rem;">
        <a href="{{ route('feedback.index') }}" class="text-decoration-none" style="color:rgba(255,255,255,0.7)"
            title="Rate Taddabur">
            <i class="bi {{ config('icons.rating') }} me-1" style="color:var(--gold)"></i>{{ $label }}
        </a>
    </p>
@endif