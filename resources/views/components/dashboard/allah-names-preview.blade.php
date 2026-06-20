{{-- resources/views/components/dashboard/allah-names-preview.blade.php --}}

<div class="d-card">

    <h5 class="d-card-title">✦ Names of Allah</h5>

    <div class="honeycomb-preview-row">
        @foreach ($names as $name)
            <div class="hex-cell" data-slug="{{ $name->slug }}">

                <div class="hex-back">
                    <span class="hex-back-arabic">{{ $name->name_ar }}</span>
                </div>

                <div class="hex-cover">
                    <span class="hex-number">{{ $name->position }}</span>
                    <span class="hex-cover-arabic">{{ $name->name_ar }}</span>
                </div>

                <div class="hex-reflection">
                    <strong>{{ $name->transliteration }}</strong>
                    <span>{{ $name->english_name }}</span>
                </div>

            </div>
        @endforeach
    </div>

    <a href="{{ route('allah-names.index') }}" class="d-explore-allah-names-link">
        View All 99 Names <i class="bi bi-arrow-right"></i>
    </a>

</div>

{{-- Shared audio element for the preview widget --}}
<audio id="namesAudioPlayer" style="display:none"></audio>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/allah-names.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/allah-names.js') }}"></script>
@endpush
