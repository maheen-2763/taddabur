{{-- resources/views/quran/partials/_search_results.blade.php --}}

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

        <div id="searchList">

            {{-- Populated dynamically by quran-index.js --}}

            <div class="search-empty-state">
                <i class="bi bi-search"></i>
                <p>Start typing to search Surahs...</p>
            </div>

        </div>

    </div>

</div>

@push('styles')
    <style>
        .search-results-container {
            max-width: 1100px;
            margin: 0 auto 2rem;
            padding: 0 1rem;
        }

        .search-results-panel {
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(201, 150, 58, 0.2);
            border-radius: 12px;
            padding: 1.25rem;
        }

        .search-results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .search-results-title {
            font-family: var(--font-heading);
            font-size: 0.8rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--gold-light);
        }

        .search-clear-btn {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.8rem;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .search-clear-btn:hover {
            color: rgba(255, 255, 255, 0.8);
        }

        .search-empty-state {
            text-align: center;
            padding: 2rem 1rem;
            color: rgba(255, 255, 255, 0.35);
        }

        .search-empty-state i {
            font-size: 1.5rem;
            display: block;
            margin-bottom: 0.75rem;
        }

        .search-empty-state p {
            margin: 0;
            font-size: 0.85rem;
        }
    </style>
@endpush
