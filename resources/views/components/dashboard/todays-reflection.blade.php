{{-- resources/views/components/dashboard/daily-ayah.blade.php --}}

@if ($dailyContent?->ayah)

    <div class="card-islamic p-4 mb-4"
        style="background:linear-gradient(135deg,var(--emerald-dark),#0a2a18); color:white;">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-start mb-4">

            <h5 class="heading-font mb-0" style="color:var(--gold-light)">
                <i class="bi bi-sun me-2"></i>
                Today's Reflection
            </h5>

            <small dir="rtl" style="color:rgba(255,255,255,.6)">
                {{ \App\Helpers\ArabicHelper::hijriDate() }}
            </small>

        </div>

        {{-- Bismillah --}}
        <div class="text-center mb-4">

            <div class="arabic-sm" style="opacity:.8">
                بِسْمِ اللّٰهِ الرَّحْمٰنِ الرَّحِيمِ
            </div>

        </div>

        {{-- Ayah --}}
        <p class="arabic text-center mb-3" style="font-size:1.8rem; line-height:2.3; color:rgba(255,255,255,.95);">

            {{ $dailyContent->ayah->text_arabic }}

        </p>

        {{-- Translation --}}
        <p class="text-center mb-4" style="font-style:italic; color:rgba(255,255,255,.75); font-size:1rem;">

            "{{ $dailyContent->ayah->translations->first()?->text }}"

        </p>

        {{-- Reflection --}}
        @if ($dailyContent->reflection)
            <div class="reflection-box mb-4">

                <small class="d-block mb-2 fw-semibold text-uppercase" style="color:var(--gold-light)">
                    Reflection
                </small>

                <p class="mb-0" style="line-height:1.8; color:rgba(255,255,255,.9);">

                    {{ Str::limit($dailyContent->reflection, 180) }}

                </p>

            </div>
        @endif

        {{-- Taddabur Prompt --}}
        <div class="mb-4">

            <small class="fw-semibold" style="color:var(--gold-light)">
                💭 Pause and
            </small>

            <small class="fw-semibold" style="color:var(--gold-light)">
                Reflect for 30 seconds
            </small>


            <p class="mb-0 mt-2" style="font-size:.95rem; color:rgba(255,255,255,.8);">

                How does this ayah connect with your life today?
                What message is Allah ﷻ inviting you to reflect upon?

            </p>

        </div>

        {{-- Footer --}}
        <div class="d-flex justify-content-between align-items-center pt-3 border-top border-light border-opacity-25">

            <small style="color:var(--gold-light)">

                {{ $dailyContent->ayah->surah->name_transliteration }}
                •

                {{ $dailyContent->ayah->surah->number }}:
                {{ $dailyContent->ayah->number }}

            </small>

            @if ($dailyContent->reflection)
                <a href="{{ route('reflections.show', $dailyContent) }}" class="btn btn-admin-primary btn-sm">

                    Read Reflection →

                </a>
            @endif

        </div>

    </div>

@endif

@push('styles')
    <style>
        .reflection-box {
            background: rgba(255, 255, 255, .08);
            border-left: 4px solid var(--gold-light);
            padding: 1rem;
            border-radius: .75rem;
            backdrop-filter: blur(6px);
        }
    </style>
@endpush
