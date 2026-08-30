{{--
    Taddabur Logo Component
    ========================
    Usage:
      @include('components.logo')                          — horizontal, 36px tall, default gold
      @include('components.logo', ['variant' => 'stacked'])  — stacked (auth/splash)
      @include('components.logo', ['height' => 24])          — custom height
      @include('components.logo', ['color' => 'dark'])       — wordmark in emerald (for light bg)

    $variant : 'horizontal' (default) | 'stacked'
    $height  : integer px, default 36
    $color   : 'gold' (default) | 'dark'
--}}

@php
    $variant = $variant ?? 'horizontal';
    $height = $height ?? 36;
    $color = $color ?? 'gold';

    $wordmarkColor = $color === 'dark' ? '#0D3D22' : '#E8BE6D';
    $arabicColor = $color === 'dark' ? '#C9963A' : '#E8BE6D';
    $ornamentColor = $color === 'dark' ? '#C9963A' : '#E8BE6D';
@endphp

{{-- Reusable 4-point sparkle ornament --}}
@php
    $ornament =
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="flex-shrink:0;" fill="' .
        $ornamentColor .
        '">
        <path d="M12 2 C12.8 8 15 10 21 12 C15 14 12.8 16 12 22 C11.2 16 9 14 3 12 C9 10 11.2 8 12 2 Z" />
    </svg>';
@endphp

@if ($variant === 'stacked')
    {{-- ── Stacked: icon above wordmark ── --}}
    <div style="display:inline-flex; flex-direction:column; align-items:center; gap:10px; line-height:1;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" height="{{ $height * 2 }}"
            width="{{ $height * 2 }}" aria-label="Taddabur" role="img">
            @include('components.logo-paths')
        </svg>
        <div style="display:inline-flex; align-items:center; gap:{{ round($height * 0.18) }}px;">
            <span
                style="font-family:'Cinzel',Georgia,serif; font-weight:700;
                        font-size:{{ round($height * 0.55) }}px; color:{{ $wordmarkColor }};
                        letter-spacing:0.1em; line-height:1;">
                TADDABUR</span>
            <span
                style="width:{{ round($height * 0.22) }}px; height:{{ round($height * 0.22) }}px; display:inline-flex;">
                {!! $ornament !!}
            </span>
            <span dir="rtl"
                style="font-family:'Amiri',serif; font-weight:580; font-size:{{ round($height * 0.42) }}px;
                        color:{{ $arabicColor }}; unicode-bidi:isolate; line-height:1;">
                تَدَبُّر</span>
        </div>
    </div>
@else
    {{-- ── Horizontal: icon beside wordmark, name in one line ── --}}
    <div style="display:inline-flex; align-items:center; gap:{{ round($height * 0.28) }}px; line-height:1;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" height="{{ $height }}"
            width="{{ $height }}" aria-label="Taddabur" role="img">
            @include('components.logo-paths')
        </svg>
        <div style="display:inline-flex; align-items:center; gap:{{ round($height * 0.16) }}px;">
            <span
                style="font-family:'Cinzel',Georgia,serif; font-weight:700;
                        font-size:{{ round($height * 0.48) }}px; color:{{ $wordmarkColor }};
                        letter-spacing:0.08em; line-height:1;">
                TADDABUR</span>
            <span
                style="width:{{ round($height * 0.2) }}px; height:{{ round($height * 0.2) }}px; display:inline-flex;">
                {!! $ornament !!}
            </span>
            <span dir="rtl"
                style="font-family:'Amiri',serif; font-size:{{ round($height * 0.4) }}px;
            font-weight:580; color:{{ $arabicColor }}; unicode-bidi:isolate; line-height:1;">
                تَدَبُّر</span>
        </div>
    </div>
@endif
