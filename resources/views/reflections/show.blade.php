@extends('layouts.app')

@section('title', 'Daily Reflection')

@section('content')

    @php
        $ayah = $dailyContent->ayah;
        $translation = $ayah?->translations->first()?->text;
    @endphp

    <div class="container py-5">

        <div class="row justify-content-center">

            <div class="col-lg-9">

                {{-- Back Button --}}
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary mb-4">
                    <i class="bi bi-arrow-left me-2"></i>
                    Back to Dashboard
                </a>

                <div class="card card-islamic border-0 shadow-sm">

                    <div class="card-body p-5">

                        {{-- Badge --}}
                        <div class="mb-4">
                            <span class="badge badge-basic">
                                Daily Reflection
                            </span>
                        </div>

                        {{-- Reference --}}
                        <div class="mb-5">

                            <h2 class="heading-font mb-2">

                                {{ $ayah?->surah?->name_transliteration }}

                            </h2>

                            <p class="text-muted mb-0">

                                Surah {{ $ayah?->surah?->number }}
                                •
                                Ayah {{ $ayah?->number }}

                            </p>

                        </div>

                        {{-- Arabic --}}
                        <div class="text-center mb-5">

                            <p class="arabic"
                                style="
                                    font-size:2.6rem;
                                    line-height:2.2;
                                    color:var(--emerald-dark);
                                ">

                                {{ $ayah?->text_arabic }}

                            </p>

                        </div>

                        {{-- Translation --}}
                        @if ($translation)
                            <div class="translation-box mb-5">

                                <p class="mb-0 fst-italic">

                                    {{ $translation }}

                                </p>

                            </div>
                        @endif

                        {{-- Reflection --}}
                        <div class="reflection-box">

                            <h4 class="heading-font mb-3">

                                💡 Reflection

                            </h4>

                            <small class="text-muted d-block mb-3">

                                Reflection on
                                {{ $ayah?->surah?->name_transliteration }}
                                {{ $ayah?->surah?->number }}:{{ $ayah?->number }}

                            </small>

                            <p class="lead mb-0" style="line-height:1.9;">

                                {{ $dailyContent->reflection }}

                            </p>

                        </div>

                        {{-- Footer --}}
                        <div class="mt-5 pt-4 border-top">

                            <small class="text-muted">

                                Published:
                                {{ $dailyContent->scheduled_for->format('d M Y') }}

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
            border-left: 4px solid var(--emerald);
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
