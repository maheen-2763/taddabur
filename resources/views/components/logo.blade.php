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
    $arabicColor = $color === 'dark' ? '#C9963A' : 'rgba(232,190,109,0.62)';
@endphp

@if ($variant === 'stacked')
    {{-- ── Stacked: icon above wordmark ── --}}
    <div style="display:inline-flex; flex-direction:column; align-items:center; gap:10px; line-height:1;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" height="{{ $height * 2 }}"
            width="{{ $height * 2 }}" aria-label="Taddabur" role="img">
            @include('components.logo-paths')
        </svg>
        <div style="text-align:center;">
            <div
                style="font-family:'Cinzel',Georgia,serif; font-weight:700;
                        font-size:{{ round($height * 0.55) }}px; color:{{ $wordmarkColor }};
                        letter-spacing:0.1em; line-height:1;">
                TADDABUR</div>
            <div
                style="font-family:'Amiri',serif; font-size:{{ round($height * 0.35) }}px;
                        color:{{ $arabicColor }}; direction:rtl; margin-top:3px;">
                تدبر</div>
        </div>
    </div>
@else
    {{-- ── Horizontal: icon beside wordmark ── --}}
    <div style="display:inline-flex; align-items:center; gap:{{ round($height * 0.28) }}px; line-height:1;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" height="{{ $height }}"
            width="{{ $height }}" aria-label="Taddabur" role="img">
            @include('components.logo-paths')
        </svg>
        <div>
            <div
                style="font-family:'Cinzel',Georgia,serif; font-weight:700;
                        font-size:{{ round($height * 0.48) }}px; color:{{ $wordmarkColor }};
                        letter-spacing:0.08em; line-height:1;">
                TADDABUR</div>
            <div
                style="font-family:'Amiri',serif; font-size:{{ round($height * 0.28) }}px;
                        color:{{ $arabicColor }}; direction:rtl; margin-top:2px;">
                تدبر</div>
        </div>
    </div>
@endif
