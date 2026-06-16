@props([
    'message' => 'No data available',
    'icon' => 'bi-inbox',
    'action' => null,
    'link' => null,
])

<div class="text-center text-muted py-4">
    <i class="bi {{ $icon }}" style="font-size:2rem; opacity:0.5"></i>

    <p class="mt-2 mb-1">{{ $message }}</p>

    @if ($action && $link)
        <a href="{{ $link }}" class="btn btn-sm btn-emerald mt-2">
            {{ $action }}
        </a>
    @endif
</div>
