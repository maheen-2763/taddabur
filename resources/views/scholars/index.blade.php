@extends('layouts.scholars')

@section('title', 'The Four Great Imams of Islamic Jurisprudence')

@section('content')
    <div class="container py-5">
        <h1 class="mb-2">The Four Great Imams</h1>
        <p class="text-muted mb-3">Founders of the four major schools of Islamic jurisprudence (Madhabs)</p>
        <hr class="imam-divider mb-5">

        <div class="row g-4">
            @foreach ($scholars as $scholar)
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm border-0 imam-card"
                        style="--madhab-color: var(--madhab-{{ $scholar->madhab }})">
                        <div class="card-body">
                            <p class="arabic-hero mb-1" dir="rtl" lang="ar">{{ $scholar->arabic_name }}</p>
                            <h5 class="card-title fw-semibold text-muted mb-3">{{ $scholar->name }}</h5>
                            <div class="mb-3">
                                <span
                                    class="badge imam-badge">{{ $scholar->madhab === 'shafi_i' ? "Shafi'i" : ucfirst($scholar->madhab) }}</span>
                                <span class="badge bg-secondary">{{ $scholar->birth_ah }}–{{ $scholar->death_ah }} AH</span>
                            </div>
                            <a href="{{ route('scholars.show', $scholar->slug) }}"
                                class="btn btn-sm btn-outline-primary stretched-link">
                                View Full Profile →
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
