@forelse($scholar->students as $student)
    <div class="list-group-item border-0 shadow-sm rounded mb-2 p-3">
        <h6 class="fw-bold mb-1">{{ $student->name }}</h6>
        @if ($student->arabic_name)
            <p class="arabic mb-1" dir="rtl" lang="ar">{{ $student->arabic_name }}</p>
        @endif
        @if ($student->description)
            <small class="text-muted">{{ $student->description }}</small>
        @endif
    </div>
@empty
    <p class="alert alert-info">No students recorded yet.</p>
@endforelse
