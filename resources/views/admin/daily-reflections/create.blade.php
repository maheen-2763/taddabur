@extends('admin.layout')
@section('title', 'Create Daily Reflection')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('admin.daily-reflections.index') }}" class="btn btn-sm btn-light">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h5 class="mb-0" style="font-family:var(--font-heading)">Create Daily Reflection</h5>
                    <small class="text-muted">Schedule a Quran reflection for a specific day.</small>
                </div>
            </div>

            <div class="admin-table p-4">
                <form action="{{ route('admin.daily-reflections.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-medium">Surah</label>
                        <select id="surah-select" name="surah_id" class="form-select" required>
                            <option value="">Select Surah</option>
                            @foreach ($surahs as $surah)
                                <option value="{{ $surah->id }}" @selected(old('surah_id') == $surah->id)>
                                    {{ $surah->number }}. {{ $surah->name_transliteration }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Ayah</label>
                        <select id="ayah-select" name="ayah_id" required
                            class="form-select @error('ayah_id') is-invalid @enderror">
                            <option value="">Select Ayah</option>
                        </select>
                        @error('ayah_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Scheduled Date</label>
                        <input type="date" name="scheduled_for" value="{{ old('scheduled_for') }}" required
                            class="form-control @error('scheduled_for') is-invalid @enderror">
                        @error('scheduled_for')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Reflection</label>
                        <textarea rows="8" name="reflection" minlength="50" maxlength="5000" required
                            class="form-control @error('reflection') is-invalid @enderror">{{ old('reflection') }}</textarea>
                        <small class="text-muted">Minimum 50 characters.</small>
                        @error('reflection')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-admin-primary">
                        <i class="bi bi-check-lg me-1"></i>Save Reflection
                    </button>
                </form>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const surahSelect = document.getElementById('surah-select');
        const ayahSelect = document.getElementById('ayah-select');
        const ayahUrl = @json(route('admin.daily-reflections.ayahs', ['surah' => '__ID__']));
        const oldAyah = @json(old('ayah_id'));

        async function loadAyahs(preselect = null) {
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
                if (preselect) ayahSelect.value = String(preselect);
            } catch (e) {
                ayahSelect.innerHTML = '<option value="">Failed to load ayahs - page refresh karein</option>';
            } finally {
                ayahSelect.disabled = false;
            }
        }

        surahSelect.addEventListener('change', () => loadAyahs());
        if (surahSelect.value) loadAyahs(oldAyah);
    </script>
@endpush
