{{-- resources/views/quran/partials/_search.blade.php --}}

<div class="quran-search-wrap">
    <i class="bi bi-search search-icon"></i>

    <input type="text" id="surahSearch" class="quran-search-input" placeholder="Search surah by name..."
        autocomplete="off" aria-label="Search Surah">
</div>

<div id="searchResults" class="search-results-container d-none">
    <div class="search-results-panel">

        <div class="search-results-header">
            <span class="search-results-title">
                Search Results
            </span>

            <button type="button" class="search-clear-btn" onclick="clearSearch()">
                <i class="bi bi-x me-1"></i>
                Clear
            </button>
        </div>

        <div id="searchList"></div>

    </div>
</div>

@push('styles')
    <style>
        .search-results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .search-results-title {
            font-family: var(--font-heading);
            color: var(--gold-light);
            font-size: 0.85rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .search-clear-btn {
            background: none;
            border: none;
            color: rgba(255, 255, 255, .4);
            font-size: .8rem;
            cursor: pointer;
        }

        .search-clear-btn:hover {
            color: rgba(255, 255, 255, .8);
        }
    </style>
@endpush
