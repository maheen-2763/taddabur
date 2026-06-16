@extends('layouts.app')
@section('title', 'The 25 Prophets — Taddabur')

@section('content')
    <section class="py-5">
        <div class="container">

            {{-- Page Header --}}
            <div class="text-center mb-5">
                <p class="text-success fw-semibold mb-1"
                    style="letter-spacing:0.12em; font-size:0.8rem; text-transform:uppercase;">
                    Stories of Faith
                </p>
                <h1 class="display-5 fw-bold">The 25 Prophets of Islam</h1>
                <p class="lead text-muted mx-auto mt-2" style="max-width:580px;">
                    Explore the lives of the prophets mentioned in the Quran — their trials,
                    miracles, and timeless lessons for humanity.
                </p>
                <hr class="divider-gold">
            </div>

            {{-- Prophets Grid --}}
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                @foreach ($prophets as $prophet)
                    <div class="col">
                        <a href="{{ route('prophets.show', $prophet) }}" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm prophet-card">

                                {{-- Cover image OR Arabic name fallback --}}
                                @if ($prophet->cover_image)
                                    <img src="{{ $prophet->cover_image_url }}" class="card-img-top"
                                        alt="{{ $prophet->display_name }}" style="height:190px; object-fit:cover;">
                                @else
                                    <div class="d-flex flex-column align-items-center
                                        justify-content-center bg-success bg-opacity-10"
                                        style="height:190px;">

                                        {{-- Arabic name large --}}
                                        <span class="arabic-name">
                                            {{ $prophet->name_arabic }}
                                        </span>

                                        {{-- Arabic honour title --}}
                                        @if ($prophet->title_arabic)
                                            <span class="arabic-title">
                                                {{ $prophet->title_arabic }}
                                            </span>
                                        @endif

                                    </div>
                                @endif

                                <div class="card-body">

                                    {{-- Order badge + honorific --}}
                                    <div
                                        class="d-flex justify-content-between
                                        align-items-center mb-2">
                                        <span
                                            class="badge rounded-pill
                                             bg-success bg-opacity-10
                                             text-success px-3">
                                            Prophet #{{ $prophet->order }}
                                        </span>
                                    </div>

                                    {{-- Name with correct honorific --}}
                                    <h5 class="card-title fw-bold text-dark mb-0">
                                        {{-- ✅ Uses model accessor --}}
                                        {{ $prophet->display_name }}
                                    </h5>
                                    <p class="text-muted small mb-1">
                                        {{ $prophet->name_english }}
                                    </p>


                                    {{-- English title --}}
                                    @if ($prophet->title)
                                        <p class="small fst-italic mb-1" style="color:var(--emerald)">
                                            {{ $prophet->title }}
                                        </p>
                                    @endif
                                    @if ($prophet->quran_mentions_count > 0)
                                        <div class="mb-2">
                                            <span class="badge bg-warning-subtle text-dark border">
                                                <i class="bi bi-book me-1"></i>
                                                Mentioned {{ $prophet->quran_mentions_count }}
                                                {{ Str::plural('time', $prophet->quran_mentions_count) }}
                                                in the Qur'an
                                            </span>
                                        </div>
                                    @endif

                                    {{-- Title transliteration --}}
                                    @if ($prophet->title_transliteration)
                                        <p class="text-muted small mb-2">
                                            {{ $prophet->title_transliteration }}
                                        </p>
                                    @endif

                                    {{-- Summary --}}
                                    @if ($prophet->summary)
                                        <p class="card-text text-muted small">
                                            {{ Str::limit($prophet->summary, 95) }}
                                        </p>
                                    @endif
                                    @if ($prophet->stories_count > 0)
                                        <div class="mt-1">
                                            <small class="text-muted">
                                                <i class="bi bi-journal-text me-1"></i>
                                                {{ $prophet->stories_count }}
                                                {{ Str::plural('story', $prophet->stories_count) }}
                                                available
                                            </small>
                                        </div>
                                    @endif
                                </div>


                                <div
                                    class="card-footer bg-transparent border-0
            d-flex justify-content-between
            align-items-center pb-2">

                                    <div>
                                        @if ($prophet->mentioned_in_quran)
                                            <small class="text-muted">
                                                <i class="bi bi-patch-check-fill text-success me-1"></i>
                                                Quranic Prophet
                                            </small>
                                        @endif
                                    </div>

                                    <small class="text-success fw-semibold">
                                        {{ $prophet->stories_count }}
                                        {{ Str::plural('story', $prophet->stories_count) }}
                                        <i class="bi bi-arrow-right ms-1"></i>
                                    </small>

                                </div>

                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    @push('styles')
        <style>
            .prophet-card {
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .prophet-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12) !important;
            }
        </style>
    @endpush

@endsection
