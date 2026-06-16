@extends('layouts.app')
@section('title', 'The Holy Quran — Taddabur')

@section('content')

    {{-- TEMP DEBUG --}}

    <div class="container-fluid py-4">

        {{-- Bismillah --}}
        <div class="text-center mb-4">
            <p
                style="font-family:var(--font-arabic);
                  font-size:2.5rem;
                  color:var(--gold-dark)">
                بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
            </p>
            <h2 class="heading-font">The Holy Quran</h2>
            <p class="text-muted">114 Surahs · 6,236 Ayahs · 30 Juz</p>
            <hr class="divider-gold">
        </div>

        {{-- Search --}}
        <div class="row justify-content-center mb-4">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--cream-dark); border-color:var(--border)">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" id="surahSearch" class="form-control" placeholder="Search surah..."
                        style="border-color:var(--border); background:var(--cream-dark)">
                </div>
            </div>
        </div>
        {{-- Add this link next to search box --}}
        <a href="{{ route('quran.search') }}" class="btn btn-sm mb-2"
            style="border:1px solid rgba(201,150,58,0.4);
          color:rgba(201,150,58,0.7);
          border-radius:50px;
          font-size:0.8rem">
            <i class="bi bi-search me-1"></i>
            Search by meaning
        </a>

        {{-- Surahs list --}}
        <div class="row g-2" id="surahGrid">
            @foreach ($surahs as $surah)
                <div class="col-12 col-md-6 col-lg-4 surah-item"
                    data-name="{{ strtolower($surah->name_transliteration . ' ' . $surah->name_english) }}">

                    <a href="{{ route('quran.show', $surah->number) }}"
                        class="card-islamic p-3 d-flex align-items-center gap-3 text-decoration-none">

                        {{-- Number --}}
                        <div
                            style="width:36px; height:36px; border:2px solid var(--gold);
                            border-radius:50%; display:flex; align-items:center;
                            justify-content:center; font-size:0.75rem;
                            font-family:var(--font-heading); color:var(--gold);
                            flex-shrink:0">
                            {{ $surah->number }}
                        </div>

                        {{-- Info --}}
                        <div class="flex-grow-1">
                            <div style="font-family:var(--font-heading); font-size:0.88rem; color:var(--ink)">
                                {{ $surah->name_transliteration }}
                            </div>
                            <div style="font-size:0.72rem; color:var(--muted)">
                                {{ $surah->name_english }} ·
                                {{ $surah->ayah_count }} ayahs ·
                                {{ ucfirst($surah->revelation_type) }}
                            </div>
                        </div>

                        {{-- Arabic --}}
                        <div
                            style="font-family:var(--font-arabic); font-size:1.3rem;
                            color:var(--emerald); direction:rtl">
                            {{ $surah->name_arabic }}
                        </div>

                    </a>
                </div>
            @endforeach
        </div>

    </div>


@endsection

@push('scripts')
    <script>
        document.getElementById('surahSearch').addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.surah-item').forEach(item => {
                item.style.display = item.dataset.name.includes(q) ? '' : 'none';
            });
        });
    </script>
@endpush
