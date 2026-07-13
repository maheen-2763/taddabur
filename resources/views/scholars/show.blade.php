@extends('layouts.scholars')

@section('title', $scholar->name)
@section('breadcrumb'){{ $scholar->name }}@endsection

@section('content')
    <div class="container py-5">

        {{-- Hero --}}
        <div class="scholar-hero text-center mb-5 p-5 rounded" style="--madhab-color: var(--madhab-{{ $scholar->madhab }})">
            <h1 class="display-4 fw-bold">{{ $scholar->name }}</h1>
            <p class="fs-4 arabic" dir="rtl" lang="ar">{{ $scholar->arabic_name }}</p>
            <div class="mt-3">
                <span class="badge imam-badge fs-6">
                    {{ $scholar->madhab === 'shafi_i' ? "SHAFI'I" : strtoupper($scholar->madhab) }}
                </span>
                <span class="badge bg-secondary fs-6">{{ $scholar->birth_ah }}–{{ $scholar->death_ah }} AH</span>
            </div>
        </div>

        {{-- Tabs --}}
        <ul class="nav nav-tabs imam-tabs mb-4" role="tablist" style="--madhab-color: var(--madhab-{{ $scholar->madhab }})">
            @foreach ([
            'biography' => 'Biography',
            'teachings' => 'Methodology',
            'quotes' => 'Quotes',
            'students' => 'Famous Students',
            'works' => 'Major Works',
            'trials' => 'Trials & Tests',
        ] as $tab => $label)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab"
                        data-bs-target="#{{ $tab }}" type="button" role="tab">
                        {{ $label }}
                    </button>
                </li>
            @endforeach
        </ul>

        {{-- Tab Content --}}
        <div class="tab-content">
            <div class="tab-pane fade show active" id="biography">
                @include('scholars.partials.biography')
            </div>
            <div class="tab-pane fade" id="teachings">
                @include('scholars.partials.teachings')
            </div>
            <div class="tab-pane fade" id="quotes">
                @include('scholars.partials.quotes')
            </div>
            <div class="tab-pane fade" id="students">
                @include('scholars.partials.students')
            </div>
            <div class="tab-pane fade" id="works">
                @include('scholars.partials.works')
            </div>
            <div class="tab-pane fade" id="trials">
                @include('scholars.partials.trials')
            </div>
        </div>

    </div>
@endsection
