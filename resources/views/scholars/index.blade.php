@extends('layouts.scholars')

@section('title', 'The Four Great Imams of Islamic Jurisprudence')

@section('content')
    <div class="container py-5">
        <h1 class="mb-2">The Four Great Imams</h1>
        <p class="text-muted mb-5">Founders of the four major schools of Islamic jurisprudence (Madhabs)</p>

        <div class="row g-4">
            @foreach ($scholars as $scholar)
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">{{ $scholar->name }}</h5>
                            <p class="arabic mb-2" dir="rtl" lang="ar">{{ $scholar->arabic_name }}</p>
                            <div class="mb-3">
                                <span class="badge bg-success">{{ ucfirst($scholar->madhab) }}</span>
                                <span class="badge bg-secondary">{{ $scholar->period }}</span>
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
