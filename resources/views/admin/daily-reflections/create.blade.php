@extends('admin.layout')

@section('title', 'Create Daily Reflection')

@section('content')

    <div class="row justify-content-center">

        <div class="col-lg-8">
            <h5 class="mb-1" style="font-family:var(--font-heading)">Create Daily Reflection</h5>

            <p class="text-muted">
                Schedule a Quran reflection for a specific day.
            </p>
        </div>

        <div class="admin-table p-4">

            <form action="{{ route('admin.daily-reflections.store') }}" method="POST">

                @csrf

                @include('admin.partials.errors')

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

                    <select id="ayah-select" name="ayah_id" class="form-select @error('ayah_id') is-invalid @enderror"
                        required>

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

                <button type="submit" class="btn-admin-primary">

                    Save Reflection

                </button>

            </form>

        </div>

    </div>

@endsection
@push('scripts')
    <script>
        const surahSelect = document.getElementById('surah-select');
        const ayahSelect = document.getElementById('ayah-select');
        const ayahUrl = @json(route('admin.daily-reflections.ayahs', ['surah' => '__ID__']));

        surahSelect.addEventListener('change', async () => {
            ayahSelect.innerHTML = '<option value="">Select Ayah</option>';
            if (!surahSelect.value) return;

            ayahSelect.disabled = true;
            try {
                const response = await fetch(ayahUrl.replace('__ID__', surahSelect.value), {
                    headers: {
                        'Accept': 'application/json'
                    },
                });
                if (!response.ok) throw new Error(response.status);

                const ayahs = await response.json();
                ayahSelect.append(...ayahs.map(a => new Option(`Ayah ${a.number}`, a.id)));
            } catch (e) {
                ayahSelect.innerHTML = '<option value="">Failed to load ayahs - page refresh karein</option>';
            } finally {
                ayahSelect.disabled = false;
            }
        });
    </script>
@endpush
