@extends('layouts.app')
@section('title', 'About — Taddabur')

@push('styles')
    <style>
        {{-- ✅ Poori purani CSS yahan same rahegi — bilkul copy-paste, kuch change nahi --}} .source-card {
            background: var(--cream-dark);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
            height: 100%;
        }

        .source-card h5 {
            font-size: 1rem;
            color: var(--ink);
        }

        .source-card .source-tag {
            display: inline-block;
            font-size: 0.75rem;
            color: var(--gold-dark);
            background: rgba(201, 150, 58, 0.1);
            border: 1px solid rgba(201, 150, 58, 0.25);
            padding: 0.2rem 0.6rem;
            border-radius: 50px;
            margin-bottom: 0.6rem;
        }

        .stats-strip {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            margin-bottom: 3rem;
        }

        .stat-block {
            flex: 1 1 220px;
            text-align: center;
            padding: 1.25rem 1rem;
            border-right: 1px solid var(--border);
            background: var(--cream-dark);
        }

        .stat-block:last-child {
            border-right: none;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--emerald-light);
            display: block;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 0.78rem;
            color: var(--ink-muted, #6b6b6b);
        }

        .accuracy-subheading {
            font-size: 0.95rem;
            color: var(--gold-dark);
            margin-bottom: 1rem;
            margin-top: 2rem;
            font-weight: 600;
        }

        .process-steps {
            counter-reset: process-counter;
            list-style: none;
            padding-left: 0;
            margin-bottom: 3rem;
        }

        .process-steps li {
            counter-increment: process-counter;
            position: relative;
            padding-left: 3rem;
            margin-bottom: 1.25rem;
        }

        .process-steps li::before {
            content: counter(process-counter);
            position: absolute;
            left: 0;
            top: 0;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            background: rgba(201, 150, 58, 0.1);
            border: 1px solid rgba(201, 150, 58, 0.35);
            color: var(--gold-dark);
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
        }

        .process-steps li strong {
            display: block;
            color: var(--ink);
            margin-bottom: 0.15rem;
        }

        .honesty-card {
            border-color: rgba(201, 150, 58, 0.35);
        }

        .honesty-quote {
            font-style: italic;
            color: var(--ink);
        }

        .audit-timestamp {
            font-size: 0.78rem;
            color: var(--ink-muted, #6b6b6b);
            text-align: center;
            margin-top: -1.5rem;
            margin-bottom: 1rem;
        }
    </style>
@endpush

@section('content')
    <section class="py-5">
        <div class="container" style="max-width: 1000px;">

            <div class="text-center mb-5">
                <h1 class="heading-font mb-2">About Taddabur</h1>
                <p class="text-muted">Built for those who want to read, reflect, and understand — with every source clearly
                    named.</p>
            </div>

            <p class="mb-4">
                Taddabur (تدبّر — "deep reflection") is a platform to read the Quran, understand its meaning through
                Tafsir, and learn from the lives of the Prophets and study authentic Hadith. We believe transparency
                about where our content comes from matters as much as the content itself.
            </p>

            {{-- Stats --}}
            <div class="stats-strip">
                @foreach ($stats as $stat)
                    <div class="stat-block">
                        <span class="stat-number">{{ $stat['number'] }}</span>
                        <span class="stat-label">{{ $stat['label'] }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Content Sources --}}
            <h4 class="heading-font mb-3">Content Sources</h4>
            <div class="row g-4 mb-5">
                @foreach ($sources as $source)
                    <div class="col-md-6">
                        <div class="source-card">
                            <span class="source-tag">{{ $source['tag'] }}</span>
                            <h5 class="mb-2">{{ $source['title'] }}</h5>

                            @if (isset($source['reciters']))
                                <div class="row row-cols-2 g-1 mb-2">
                                    @foreach ($source['reciters'] as $reciter)
                                        <div class="col"><small class="text-muted">• {{ $reciter }}</small></div>
                                    @endforeach
                                </div>
                            @endif

                            <p class="text-muted small mb-0">{{ $source['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Accuracy --}}
            <h4 class="heading-font mb-2">How We Ensure Accuracy</h4>
            <p class="text-muted small mb-0">We aim for a 0% error rate across everything we publish. Here's how each part
                of the app is checked.</p>

            @foreach ($accuracy as $sectionTitle => $items)
                <div class="accuracy-subheading">{{ $sectionTitle }}</div>
                <div class="row g-4 mb-2">
                    @foreach ($items as $item)
                        <div class="col-md-6">
                            <div class="source-card">
                                <span class="source-tag">{{ $item['tag'] }}</span>
                                <h5 class="mb-2">{{ $item['title'] }}</h5>
                                <p class="text-muted small mb-0">{{ $item['description'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach

            {{-- Rooted in Honesty --}}
            <h4 class="heading-font mb-3 mt-3">Rooted in Honesty</h4>
            <div class="source-card honesty-card mb-5">
                <p class="honesty-quote mb-2">"{{ $honestyQuote['text'] }}"</p>
                <p class="text-muted small mb-0">
                    — {{ $honestyQuote['narrator'] }}
                    (<a href="{{ route('hadith.show', ['collection' => $honestyQuote['hadith_collection'], 'chapter' => $honestyQuote['hadith_chapter']]) }}?highlight={{ $honestyQuote['hadith_highlight_id'] }}"
                        style="color:var(--gold-dark)">{{ $honestyQuote['hadith_label'] }}</a>)
                </p>
            </div>

            {{-- Our Process — static, rarely changes, kept as-is --}}
            <h4 class="heading-font mb-3">Our Process</h4>
            <ol class="process-steps">
                <li><strong>Sourcing</strong> Content is drawn from established, widely-used open sources — never generated
                    or paraphrased by us.</li>
                <li><strong>Auditing</strong> Every collection is run through automated checks, followed by manual review of
                    anything flagged.</li>
                <li><strong>Honest Flagging</strong> Where something can't be verified, we mark it clearly rather than
                    hiding the gap or guessing.</li>
            </ol>

            <h4 class="heading-font mb-3">Found an Error?</h4>
            <p class="text-muted">
                If you notice any inaccuracy, please <a href="mailto:your-email@example.com"
                    style="color:var(--gold-dark)">let us know</a>.
                Every report is reviewed against the original source before any correction is made.
            </p>

            <p class="audit-timestamp mt-4">{{ $auditTimestamp }}</p>

        </div>
    </section>
@endsection
