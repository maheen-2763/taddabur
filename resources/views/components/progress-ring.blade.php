{{-- resources/views/components/progress-ring.blade.php --}}
@props(['percent' => 0, 'size' => 36])

@php
    $radius = $size / 2 - 3;
    $circumference = 2 * pi() * $radius;
    $offset = $circumference - ($percent / 100) * $circumference;
    $strokeClass = $percent == 100 ? 'ring-complete' : ($percent > 0 ? 'ring-progress' : 'ring-empty');
@endphp

<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 {{ $size }} {{ $size }}"
    class="progress-ring {{ $strokeClass }}">
    <circle cx="{{ $size / 2 }}" cy="{{ $size / 2 }}" r="{{ $radius }}" class="ring-bg" fill="none"
        stroke-width="3" />
    <circle cx="{{ $size / 2 }}" cy="{{ $size / 2 }}" r="{{ $radius }}" class="ring-fill" fill="none"
        stroke-width="3" stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}"
        stroke-linecap="round" transform="rotate(-90 {{ $size / 2 }} {{ $size / 2 }})" />
    <text x="{{ $size / 2 }}" y="{{ $size / 2 + 3 }}" text-anchor="middle"
        class="ring-text">{{ $percent }}%</text>
</svg>
