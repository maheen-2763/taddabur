@extends('layouts.app')

@section('title', 'Daily Reflection')

@section('content')

    @php
        $isAyah = $dailyContent->type === 'ayah';
        $ayah = $dailyContent->ayah;
        $hadith = $dailyContent->hadith;
        $translation = $ayah?->translations->first()?->text;
    @endphp

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">

                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary mb-4">
                    <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
                </a>

                <div class="card card-islamic border-0 shadow-sm">
                    <div class="card-body p-5">

                        <div class="mb-4">
                            <span class="badge badge-basic">
                                {{ $isAyah ? 'Daily Reflection' : 'Hadith of the Day' }}
                            </span>
                        </div>

                        @if ($isAyah)
                            {{-- ================= AYAH BLOCK (existing, unchanged) ================= --}}
                            <div class="mb-5">
                                <h2 class="heading-font mb-2">{{ $ayah?->surah?->name_transliteration }}</h2>
                                <p class="text-muted mb-0">
                                    Surah {{ $ayah?->surah?->number }} • Ayah {{ $ayah?->number }}
                                </p>
                            </div>

                            <div class="text-center mb-5">
                                <p class="arabic" style="font-size:2.6rem; line-height:2.2; color:var(--emerald-dark);">
                                    {{ $ayah?->text_arabic }}
                                </p>
                            </div>

                            @if ($translation)
                                <div class="translation-box mb-5">
                                    <p class="mb-0 fst-italic">{{ $translation }}</p>
                                </div>
                            @endif
                        @else
                            {{-- ================= HADITH BLOCK (final) ================= --}}
                            <div class="mb-2">
                                <span
                                    class="hadith-badge {{ $hadith?->collection_id === 1 ? 'badge-bukhari' : 'badge-muslim' }}">
                                    {{ $hadith?->collection?->name }}
                                </span>
                            </div>

                            <p class="text-muted mb-5">
                                {{ $hadith?->chapter?->title }} • Hadith #{{ $hadith?->number }}
                            </p>

                            <div class="text-center mb-4">
                                <p class="arabic" style="font-size:2rem; line-height:2; color:var(--emerald-light);">
                                    {{ $hadith?->arabic }}
                                </p>
                            </div>

                            <div class="translation-box mb-4">
                                <p class="mb-0 fst-italic">{{ $hadith?->english }}</p>
                            </div>

                            @if ($hadith?->narrator_chain)
                                <p class="text-muted small mb-5">
                                    <i class="bi bi-link-45deg"></i>
                                    Narrated via: {{ Str::limit($hadith->narrator_chain, 80) }}
                                </p>
                            @endif
                        @endif
                        {{-- Reflection block — same for both types --}}
                        @if ($dailyContent->reflection)
                            <div class="reflection-box">
                                <h4 class="heading-font mb-3">💡 Reflection</h4>
                                <p class="lead mb-0" style="line-height:1.9;">{{ $dailyContent->reflection }}</p>
                            </div>
                        @endif

                        <div class="mt-5 pt-4 border-top">
                            <small class="text-muted">
                                Published: {{ $dailyContent->scheduled_for->format('d M Y') }}
                            </small>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .translation-box {
            background: rgba(27, 94, 59, 0.06);
            border-left: 4px solid var(--emerald-light);
            padding: 1.25rem;
            border-radius: 12px;
        }

        .reflection-box {
            background: var(--cream);
            border-left: 4px solid var(--gold);
            padding: 1.5rem;
            border-radius: 12px;
        }
    </style>
@endpush
