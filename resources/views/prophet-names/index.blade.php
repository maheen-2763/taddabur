@extends('layouts.app')
@section('title', 'Names of the Prophet ﷺ — Taddabur')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/prophet-names.css') }}">
@endpush

@section('content')
    <div class="pname-wrap">

        <div class="pname-header">
            <span class="arabic-title">أَسْمَاءُ النَّبِيِّ ﷺ</span>
            <span class="latin-title">Names & Titles of the Prophet Muhammad ﷺ</span>
            <p class="verse-ref">Verified directly from the Qur'an and Sahih Hadith — no external compilations.</p>
        </div>

        <div class="pname-tier-label">
            <span class="arabic">الأَسْمَاء</span>
            <span class="latin">His Names</span>
        </div>

        <div class="pname-timeline">
            @foreach ($names as $index => $name)
                <x-prophet-name-card :item="$name" :index="$index" />
            @endforeach
        </div>

        <div class="pname-tier-label pname-tier-titles">
            <span class="arabic">الأَلْقَاب</span>
            <span class="latin">Titles Mentioned in the Qur'an</span>
        </div>

        <div class="pname-timeline">
            @foreach ($titles as $index => $title)
                <x-prophet-name-card :item="$title" :index="$index" />
            @endforeach
        </div>

    </div>
@endsection
