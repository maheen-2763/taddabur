@extends('layouts.app')
@section('title', 'The 25 Prophets of Islam — Taddabur')

@push('styles')
    <style>
        /* ── Page header ── */
        .prophets-header {
            text-align: center;
            padding: 3rem 0 2.5rem;
        }

        /* ── Prophet card ── */
        .prophet-card {
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: var(--cream);
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .prophet-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
            color: inherit;
        }

        /* ── Card image / Arabic fallback ── */
        .prophet-card-cover {
            height: 160px;
            background: linear-gradient(150deg, var(--emerald-dark), var(--emerald));
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .prophet-card-cover::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        .prophet-card-cover-arabic {
            font-family: 'Amiri', 'Scheherazade New', serif;
            font-size: 2.5rem;
            color: white;
            line-height: 1.3;
            text-align: center;
            margin-bottom: 0.3rem;
            position: relative;
            z-index: 1;
        }

        .prophet-card-cover-title {
            font-family: 'Amiri', 'Scheherazade New', serif;
            font-size: 0.95rem;
            color: var(--gold-light);
            text-align: center;
            margin-top: 0.45rem;
            position: relative;
            z-index: 1;
        }

        .prophet-card-cover-order {
            position: absolute;
            top: 0.75rem;
            left: 0.75rem;
            font-size: 0.68rem;
            color: rgba(255, 255, 255, 0.55);
            background: rgba(0, 0, 0, 0.2);
            border-radius: 50px;
            padding: 0.2rem 0.6rem;
            letter-spacing: 0.05em;
        }

        /* ── Card body ── */
        .prophet-card-body {
            padding: 1.1rem 1.25rem 1.25rem;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .prophet-card-name {
            font-family: var(--font-heading);
            font-size: 1rem;
            font-weight: 580;
            color: var(--ink);
            margin-bottom: 0.15rem;
            line-height: 1.3;
        }

        .prophet-card-english {
            font-size: 0.78rem;
            color: var(--muted);
            margin-bottom: 0.5rem;
        }

        .prophet-card-title {
            font-size: 0.8rem;
            color: var(--emerald);
            font-style: italic;
            margin-bottom: 0.6rem;
            line-height: 1.4;
        }

        [data-bs-theme="dark"] .prophet-card-title {
            color: var(--emerald-light);

        }


        .prophet-card-summary {
            font-size: 0.82rem;
            color: var(--ink-soft);
            line-height: 1.6;
            flex: 1;
            margin-bottom: 0.75rem;
        }

        /* ── Card footer ── */
        .prophet-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 0.75rem;
            border-top: 1px solid var(--border);
            margin-top: auto;
        }

        .prophet-chip {
            font-size: 0.7rem;
            padding: 0.2rem 0.6rem;
            border-radius: 50px;
            font-weight: 500;
        }

        .prophet-chip-quran {
            background: rgba(27, 94, 59, 0.08);
            color: var(--emerald);
        }

        [data-bs-theme="dark"] .prophet-chip-quran {
            color: var(--emerald-light);

        }



        .prophet-chip-soon {
            background: rgba(180, 130, 40, 0.08);
            color: var(--gold);
        }


        /* ── Mobile responsiveness ── */
        @media (max-width: 576px) {
            .prophets-header {
                padding: 2rem 0 1.5rem;
            }

            .prophet-card-cover {
                height: 130px;
            }

            .prophet-card-cover-arabic {
                font-size: 2rem;
            }

            .prophet-card-cover-title {
                font-size: 0.75rem;
            }

            .prophet-card-cover-order {
                font-size: 0.62rem;
                top: 0.5rem;
                left: 0.5rem;
                padding: 0.15rem 0.5rem;
            }

            .prophet-card-body {
                padding: 0.9rem 1rem 1rem;
            }

            .prophet-card-name {
                font-size: 0.92rem;
            }

            .prophet-card-summary {
                font-size: 0.78rem;
                -webkit-line-clamp: 3;
                display: -webkit-box;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .prophet-card-footer {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .prophet-chip {
                font-size: 0.65rem;
                padding: 0.15rem 0.5rem;
            }
        }

        /* ── Extra-small screens (very old/small phones) ── */
        @media (max-width: 360px) {
            .prophet-card-cover-arabic {
                font-size: 1.7rem;
            }

            h1.heading-font {
                font-size: 1.6rem !important;
            }
        }

        @media (max-width: 576px) {
            .pagination {
                font-size: 0.8rem;
                flex-wrap: wrap;
                justify-content: center;
            }

            .page-link {
                padding: 0.4rem 0.65rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container px-3 px-sm-4 py-4 prophets-wrap">

        {{-- ═══════════════════════════════════════════════
         PAGE HEADER
    ════════════════════════════════════════════════ --}}
        <div class="prophets-header">
            <p
                style="color:var(--gold);
                  font-family:var(--font-heading);
                  font-size:1rem;
                  letter-spacing:0.14em;
                  text-transform:uppercase;
                  margin-bottom:0.5rem">
                Stories of Faith
            </p>
            <h1 class="heading-font mb-3" style="font-size:clamp(2rem,5vw,3rem)">
                The 25 Prophets of Islam
            </h1>
            <p class="text-muted mx-auto" style="max-width:520px; line-height:1.8; font-size:1rem">
                Explore the lives of the prophets sent by Allah — their trials,
                miracles, and timeless lessons. Every detail is clearly marked as
                coming from the Quran, authentic Hadith, or scholarly tradition.
            </p>

            {{-- Decorative divider --}}
            <div class="d-flex align-items-center justify-content-center gap-3 mt-3">
                <div style="width:40px; height:1px; background:var(--gold); opacity:0.4"></div>
                <span style="font-family:'Amiri',serif; font-size:1.4rem; color:var(--gold); opacity:0.8">﷽</span>
                <div style="width:40px; height:1px; background:var(--gold); opacity:0.4"></div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
         PROPHETS GRID
    ════════════════════════════════════════════════ --}}
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4 mt-1">
            @foreach ($prophets as $prophet)
                <div class="col">
                    <a href="{{ route('prophets.show', $prophet) }}" class="prophet-card">

                        {{-- Cover --}}
                        @if ($prophet->cover_image)
                            <img src="{{ $prophet->cover_image_url }}" alt="{{ $prophet->display_name }}"
                                style="height:160px; width:100%; object-fit:cover">
                        @else
                            <div class="prophet-card-cover">
                                <span class="prophet-card-cover-order">
                                    {{ $prophet->order }} of 25
                                </span>
                                <div class="prophet-card-cover-arabic">
                                    {!! \App\Helpers\ArabicHelper::wrapHonorifics($prophet->name_arabic) !!}
                                </div>
                                @if ($prophet->title_arabic)
                                    <div class="prophet-card-cover-title">
                                        {{ $prophet->title_arabic }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Body --}}
                        <div class="prophet-card-body">

                            <div class="prophet-card-name">
                                {!! \App\Helpers\ArabicHelper::wrapHonorifics($prophet->display_name) !!}
                            </div>
                            <div class="prophet-card-english">
                                {!! \App\Helpers\ArabicHelper::wrapHonorifics($prophet->name_english) !!}
                            </div>

                            @if ($prophet->title)
                                <div class="prophet-card-title">
                                    {!! \App\Helpers\ArabicHelper::wrapHonorifics($prophet->title) !!}
                                </div>
                            @endif

                            {{-- summary --}}
                            @if ($prophet->summary)
                                <div class="prophet-card-summary">
                                    {!! \App\Helpers\ArabicHelper::wrapHonorifics(Str::limit($prophet->summary, 95)) !!}
                                </div>
                            @endif

                            {{-- Footer --}}
                            <div class="prophet-card-footer">
                                <div class="d-flex gap-2 flex-wrap">
                                    @if ($prophet->quran_mentions_count > 0)
                                        <span class="prophet-chip prophet-chip-quran">
                                            <i class="bi bi-book-half me-1"></i>
                                            {{ $prophet->quran_mentions_count }}x in Qur'an
                                        </span>
                                    @endif
                                    @if ($prophet->stories_count > 0)
                                        <span class="prophet-chip prophet-chip-quran">
                                            <i class="bi bi-journal-text me-1"></i>
                                            {{ $prophet->stories_count }}
                                            {{ Str::plural('story', $prophet->stories_count) }}
                                        </span>
                                    @else
                                        <span class="prophet-chip prophet-chip-soon">
                                            <i class="bi bi-hourglass-split me-1"></i>
                                            Coming soon
                                        </span>
                                    @endif
                                </div>

                                <span style="font-size:0.8rem; color:var(--emerald-light); font-weight:500">
                                    View <i class="bi bi-arrow-right ms-1"></i>
                                </span>
                            </div>

                        </div>
                    </a>
                </div>
            @endforeach
        </div>
        {{-- ✅ NEW — Pagination --}}
        <div class="d-flex justify-content-center mt-5">
            {{ $prophets->links() }}
        </div>

    </div>
@endsection
