@extends('layouts.app')
@section('title', 'Features — Taddabur')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/features.css') }}">
@endpush

@section('content')
    <div class="feat-wrap">

        <div class="feat-header">
            <span class="arabic-title">مَا يُقَدِّمُهُ تَدَبُّر</span>
            <span class="latin-title">What Taddabur Offers</span>
            <p class="verse-ref">A complete companion for reading, understanding, and reflecting on the Qur'an and Sunnah.
            </p>
        </div>

        <div class="feat-grid">
            @foreach ($features as $feature)
                <div class="feat-arch">
                    <div class="feat-arch-inner">
                        <div class="feat-arch-icon">
                            <i class="bi {{ $feature['icon'] }}"></i>
                        </div>
                        <span class="feat-tag">{{ $feature['tag'] }}</span>
                        <h3 class="feat-title">{{ $feature['title'] }}</h3>
                        <p class="feat-desc">{{ $feature['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
@endsection
