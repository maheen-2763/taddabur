@php $tone = $tone ?? 'emerald'; @endphp
<div class="stat-card">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <div class="stat-label">{{ $label }}</div>
            <div class="stat-number" style="color:var(--{{ $tone }})">{{ $value }}</div>
        </div>
        <div class="stat-icon" style="background:{{ $tone === 'gold' ? 'rgba(201,150,58,.1)' : 'rgba(27,94,59,.1)' }}">
            <i class="bi bi-{{ $icon }}" style="color:var(--{{ $tone }})"></i>
        </div>
    </div>
    @isset($note)
        <small class="text-muted">{{ $note }}</small>
    @endisset
</div>
