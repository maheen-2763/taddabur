@extends('admin.layout')

@section('title', 'Create Daily Reflection')

@section('content')

    <div class="container-fluid">

        <div class="mb-4">
            <h1 class="page-title">
                Create Daily Reflection
            </h1>

            <p class="text-muted">
                Schedule a Quran reflection for a specific day.
            </p>
        </div>

        <div class="card card-islamic">
            <div class="card-body">

                <form action="{{ route('admin.daily-reflections.store') }}" method="POST">

                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Surah</label>

                        <select id="surah-select" class="form-select" required>

                            <option value="">
                                Select Surah
                            </option>

                            @foreach ($surahs as $surah)
                                <option value="{{ $surah->id }}">
                                    {{ $surah->number }}.
                                    {{ $surah->name_transliteration }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    {{-- Ayah --}}
                    <div class="mb-3">


                        <label class="form-label">
                            Ayah
                        </label>

                        <select id="ayah-select" name="ayah_id" class="form-select" required>

                            <option value="">
                                Select Ayah
                            </option>

                        </select>

                        @error('ayah_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- Date --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Scheduled Date
                        </label>

                        <input type="date" name="scheduled_for" value="{{ old('scheduled_for') }}"
                            class="form-control @error('scheduled_for') is-invalid @enderror" required>

                        @error('scheduled_for')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- Reflection --}}
                    <div class="mb-4">

                        <label class="form-label">
                            Reflection
                        </label>

                        <textarea rows="8" name="reflection" class="form-control @error('reflection') is-invalid @enderror" required>{{ old('reflection') }}</textarea>

                        @error('reflection')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <button type="submit" class="btn btn-islamic">

                        Save Reflection

                    </button>

                </form>

            </div>
        </div>

    </div>

@endsection
@push('scripts')
    <script>
        document
            .getElementById('surah-select')
            .addEventListener('change', async function() {

                let surahId = this.value;

                let ayahSelect =
                    document.getElementById('ayah-select');

                ayahSelect.innerHTML =
                    '<option>Loading...</option>';

                let response =
                    await fetch(
                        `/admin/ayahs/${surahId}`
                    );

                let ayahs =
                    await response.json();

                ayahSelect.innerHTML =
                    '<option value="">Select Ayah</option>';

                ayahs.forEach(ayah => {

                    ayahSelect.innerHTML += `
                <option value="${ayah.id}">
                    Ayah ${ayah.number}
                </option>
            `;

                });

            });
    </script>
@endpush
