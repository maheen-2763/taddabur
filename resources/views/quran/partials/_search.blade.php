{{-- resources/views/quran/partials/_search.blade.php --}}

{{-- Search input --}}
<div class="quran-search-wrap">
    <i class="bi bi-search"></i>
    <input type="text" id="surahSearch" placeholder="Search surah by name..." autocomplete="off">
</div>

{{-- Search results panel (hidden by default) --}}
<div class="container" id="searchResults" style="display:none">
    <div class="search-results-panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span
                style="font-family:var(--font-heading);
                         color:var(--gold-light);
                         font-size:0.85rem;
                         letter-spacing:0.05em">
                SEARCH RESULTS
            </span>
            <button onclick="clearSearch()"
                style="background:none; border:none;
                           color:rgba(255,255,255,0.4);
                           font-size:0.8rem; cursor:pointer">
                <i class="bi bi-x me-1"></i>Clear
            </button>
        </div>
        <div id="searchList"></div>
    </div>
</div>
