{{-- resources/views/components/dashboard/daily-ayah.blade.php --}}

@if (!empty($dailyContent?->ayah))
    <div class="card-islamic p-4 mb-4"
        style="background:linear-gradient(135deg, var(--emerald-dark), #0a2a18); color:white">

        <div class="d-flex justify-content-between align-items-start mb-3">
            <h5 class="heading-font mb-0" style="color:var(--gold-light)">
                <i class="bi bi-sun me-2"></i>Today's Reflection
            </h5>
            <small dir="rtl" style="color:rgba(255,255,255,0.5)">
                {{ \App\Helpers\ArabicHelper::hijriDate() }}
            </small>
        </div>

        <p class="arabic mb-1" style="font-size:1.6rem; color:rgba(255,255,255,0.9)">
            {{ $dailyContent->ayah->text_arabic }}
        </p>

        <p style="font-style:italic; color:rgba(255,255,255,0.7); font-size:0.95rem">
            {{ $dailyContent->ayah->translations->first()?->text }}
        </p>


        @if ($dailyContent->reflection)
            <div class="mt-4 pt-3 border-top border-light border-opacity-25">

                <p class="mb-3" style="color:rgba(255,255,255,.85); line-height:1.8;">
                    {{ Str::limit($dailyContent->reflection, 180) }}
                </p>

                <a href="{{ route('reflections.show', $dailyContent) }}" class="btn btn-admin-primary">
                    Read Reflection →
                </a>

            </div>
        @endif


        <small style="color:var(--gold-light)">
            — {{ $dailyContent->ayah->surah->name_transliteration }}
            {{ $dailyContent->ayah->surah->number }}:{{ $dailyContent->ayah->number }}
        </small>
    </div>
@endif
@push('styles')
    <style>
        .reflection-box {
            background: rgba(255, 255, 255, .08);
            border-left: 3px solid var(--gold-light);
            padding: 1rem;
            border-radius: .75rem;
        }
    </style>
@endpush
