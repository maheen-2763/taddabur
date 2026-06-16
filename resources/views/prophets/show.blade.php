@extends('layouts.app')
@section('title', $prophet->display_name . ' — Taddabur')

@section('content')
    <div class="container py-5">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('prophets.index') }}" class="text-decoration-none" style="color:var(--emerald)">
                        Prophets
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    {{ $prophet->display_name }}
                </li>
            </ol>
        </nav>

        {{-- Prophet Hero Card --}}
        <div class="card-islamic p-0 mb-5 overflow-hidden">
            <div class="row g-0">

                {{-- Cover image OR Arabic fallback --}}
                <div class="col-md-4">
                    @if ($prophet->cover_image)
                        <img src="{{ $prophet->cover_image_url }}" alt="{{ $prophet->display_name }}"
                            class="img-fluid w-100 h-100" style="object-fit:cover; min-height:280px;">
                    @else
                        <div class="d-flex flex-column
                                        align-items-center
                                        justify-content-center
                                        h-100 text-center"
                            style="min-height:280px;
                                       background:linear-gradient(
                                            135deg,
                                            var(--emerald-dark),
                                            var(--emerald)
                                       );">

                            {{-- Decorative Icon --}}
                            <i class="bi bi-stars"
                                style="font-size:3rem;
                                           color:var(--gold-light);
                                           opacity:.9;">
                            </i>

                            {{-- Prophet Order --}}
                            <span class="mb-3 badge"
                                style="background:rgba(255,255,255,.12);
                                           color:white;
                                           padding:.55rem 1rem;">
                                Prophet #{{ $prophet->order }} of 25
                            </span>


                            <span class="mb-2 text-white-50" style="font-size:0.85rem">
                                Life Journery Highlights
                            </span>
                            @if (!empty($prophet->timeline))

                                <div class="timeline-section">

                                    @foreach ($prophet->timeline ?? [] as $event)
                                        <div class="mb-2">
                                            <span class="small">{{ $event['title'] }}</span>

                                            <span class="text-white-50 small">
                                                • {{ $event['period'] }}
                                            </span>
                                        </div>
                                    @endforeach

                                </div>

                            @endif




                            {{-- Decorative Divider --}}
                            <div
                                style="
                                    width:70px;
                                    height:2px;
                                    background:var(--gold-light);
                                    margin-top:1rem;
                                    opacity:.8;">


                            </div>
                            {{-- Quran Mentions --}}
                            @if ($prophet->mentioned_in_quran)
                                <div class="mt-4">
                                    <div
                                        style="
                                                        font-size:3rem;
                                                        font-weight:700;
                                                        color:white;
                                                        line-height:1;">
                                        {{ $prophet->quran_mentions_count > 0 }}
                                    </div>

                                    <div
                                        style="
                                                        color:rgba(255,255,255,.75);
                                                        font-size:.85rem;
                                                        letter-spacing:.08em;
                                                        text-transform:uppercase;">
                                        Mentions in Qur'an
                                    </div>
                                </div>
                            @endif

                            {{-- Stories Count --}}
                            <div class="mt-4">
                                <span
                                    style="
                        color:white;
                        font-size:1.15rem;
                        font-weight:600;">
                                    {{ $prophet->stories_count }}
                                </span>

                                <div
                                    style="
                        color:rgba(255,255,255,.7);
                        font-size:.85rem;">
                                    {{ Str::plural('Story', $prophet->stories_count) }}
                                    Available
                                </div>
                            </div>

                        </div>
                    @endif


                </div>


                {{-- Prophet info right --}}
                <div class="col-md-8">
                    <div class="p-4 p-md-5">

                        <div
                            class="d-flex align-items-start
                                        justify-content-between
                                        flex-wrap gap-3 mb-3">

                            <div>
                                {{-- Name with correct honorific --}}
                                <h1 class="heading-font mb-1" style="font-size:clamp(1.5rem, 4vw, 2.2rem)">
                                    {{ $prophet->display_name }}
                                </h1>

                                <p class="text-muted mb-0">{{ $prophet->name_english }}</p>
                            </div>

                            {{-- Arabic name right side --}}
                            <div style="text-align:right">
                                <span
                                    style="font-family:'Amiri', serif;
                                                 font-size:2.8rem;
                                                 direction:rtl;
                                                 unicode-bidi:bidi-override;
                                                 color:var(--emerald);
                                                 display:block;
                                                 line-height:1.4 margin-bottom:0.3rem">
                                    {{ $prophet->name_arabic }}
                                </span>

                                {{-- Arabic honour title --}}
                                @if ($prophet->title_arabic)
                                    <span
                                        style="font-family:'Amiri', serif;
                                                             font-size:1.1rem;
                                                             direction:rtl;
                                                             unicode-bidi:bidi-override;
                                                             color:var(--gold);
                                                             display:block;
                                                             text-align:right;
                                                             line-height:1.6">
                                        {{ $prophet->title_arabic }}
                                    </span>
                                    <small
                                        style="display:block;
                                                              text-align:right;
                                                              color:#999;
                                                              font-size:0.75rem">
                                        {{ $prophet->title_transliteration }}
                                    </small>
                                @endif
                            </div>
                        </div>

                        {{-- English title --}}
                        @if ($prophet->title)
                            <p class="mb-2" style="color:var(--emerald); font-weight:500">
                                {{ $prophet->title }}
                            </p>
                        @endif

                        {{-- Period --}}

                        {{-- Summary --}}
                        @if ($prophet->summary)
                            <p class="mb-3" style="color:var(--ink-soft); line-height:1.8">
                                {{ $prophet->summary }}
                            </p>
                        @endif
                        <div class="card-islamic p-2 mt-3 mb-2"
                            style="background:rgba(27,94,59,0.06); border-radius:var(--radius)">

                            <div class="text-center">

                                <div class="arabic-text mb-2" style="font-size:2rem;line-height:2.2;color:var(--emerald);">

                                    وَعَلَّمَ آدَمَ الْأَسْمَاءَ كُلَّهَا

                                </div>

                                <p class="mb-2 fw-semibold">
                                    "And He taught Adam the names of all things."
                                </p>

                                <small class="text-muted">
                                    Surah Al-Baqarah 2:31
                                </small>

                            </div>

                        </div>

                        {{-- Quran references --}}
                        @if ($prophet->mentioned_in_quran)
                            <div class="mb-3 p-2"
                                style="background:rgba(27,94,59,0.06);
                                    border-radius:var(--radius);
                                    border-left:3px solid var(--emerald)">
                                <small style="color:var(--emerald)">
                                    <i class="bi bi-book me-1"></i>
                                    Mentioned {{ $prophet->quran_mentions_count > 0 }}
                                    {{ Str::plural('time', $prophet->quran_mentions_count) }}
                                    in the Holy Qur'an
                                </small>
                            </div>
                        @endif
                        {{-- Badges --}}
                        <div class="d-flex gap-2 flex-wrap">

                            <span class="badge bg-light text-dark border">
                                #{{ $prophet->order }} of 25 Prophets
                            </span>

                            @if ($prophet->mentioned_in_quran > 0)
                                <span class="badge" style="background:var(--emerald); padding:0.4rem 0.8rem">
                                    <i class="bi bi-book me-1"></i>
                                    Quranic Prophet
                                </span>
                            @endif

                            <span class="badge bg-secondary px-3 py-2">
                                {{ $prophet->stories_count }}
                                {{ Str::plural('Story', $prophet->stories_count) }}
                            </span>

                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Stories Section --}}
        @if ($stories->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-journal-text" style="font-size:3rem; opacity:0.3; display:block; margin-bottom:1rem"></i>
                <p class="small text-muted">
                    New content is added regularly. Check back soon, in shaa Allah.
                </p>
                <p>We're working on content for {{ $prophet->display_name }}.</p>
                <a href="{{ route('prophets.index') }}" class="btn-emerald btn mt-2">
                    <i class="bi bi-arrow-left me-1"></i>Back to Prophets
                </a>
            </div>
        @else
            <h3 class="heading-font mb-4">
                Stories of {{ $prophet->name_transliteration }}
            </h3>

            <div class="row row-cols-1 row-cols-md-2 g-4">
                @foreach ($stories as $story)
                    <div class="col">
                        <div class="card-islamic p-4 h-100">

                            <div
                                class="d-flex justify-content-between
                                                        align-items-start mb-2">
                                <h5 class="heading-font mb-0" style="font-size:1rem">
                                    <a href="{{ route('stories.show', $story->slug) }}" class="text-decoration-none"
                                        style="color:var(--ink)">
                                        {{ $story->title }}
                                    </a>
                                </h5>
                                @if (!$story->is_free)
                                    <span class="badge ms-2 flex-shrink-0" style="background:var(--gold); color:#1A1A2E">
                                        <i class="bi bi-stars me-1"></i>
                                        Premium
                                    </span>
                                @else
                                    <span class="badge bg-success ms-2 flex-shrink-0">
                                        Free
                                    </span>
                                @endif
                            </div>

                            @if ($story->summary)
                                <p class="text-muted mb-3" style="font-size:0.88rem">
                                    {{ Str::limit($story->summary, 120) }}
                                </p>
                            @endif

                            <div class="d-flex gap-2 flex-wrap mb-3">
                                @if ($story->difficulty)
                                    <span class="badge bg-light text-muted border">
                                        {{ ucfirst($story->difficulty) }}
                                    </span>
                                @endif
                                <span class="badge bg-light text-muted border">
                                    <i class="bi bi-journal-text me-1"></i>
                                    {{ $story->chapters_count }}
                                    Chapters
                                </span>
                                @if ($story->read_time_minutes)
                                    <span class="badge bg-light text-muted border">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ $story->read_time_minutes }} min
                                    </span>
                                @endif
                            </div>

                            {{-- CTA Button --}}
                            @if ($story->is_free)
                                <a href="{{ route('stories.show', $story->slug) }}" class="btn-emerald btn btn-sm">
                                    Read Story <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            @else
                                @auth
                                    @if (auth()->user()->isPremium())
                                        <a href="{{ route('stories.show', $story->slug) }}" class="btn-emerald btn btn-sm">
                                            Read Story <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('subscription.upgrade') }}" class="btn-gold btn btn-sm">
                                            <i class="bi bi-lock me-1"></i>Upgrade to Read
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="btn-emerald btn btn-sm">
                                        Sign in to Read
                                    </a>
                                @endauth
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-5">
                <a href="{{ route('prophets.index') }}" class="text-decoration-none" style="color:var(--emerald)">
                    <i class="bi bi-arrow-left me-1"></i>All Prophets
                </a>
            </div>
        @endif

    </div>
@endsection
