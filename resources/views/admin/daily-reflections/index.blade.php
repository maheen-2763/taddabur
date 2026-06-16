@extends('admin.layout')

@section('title', 'Daily Reflections')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title">Daily Reflections</h1>
                <p class="text-muted">
                    Manage scheduled Quran reflections
                </p>
            </div>

            <a href="{{ route('admin.daily-reflections.create') }}" class="btn btn-islamic">
                + New Reflection
            </a>
        </div>

        <div class="card card-islamic">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table align-middle">

                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Surah</th>
                                <th>Ayah</th>
                                <th>Reflection</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($dailyContents as $content)
                                <tr>

                                    <td>
                                        {{ $content->scheduled_for->format('d M Y') }}
                                    </td>

                                    <td>
                                        {{ $content->ayah?->surah?->name }}
                                    </td>

                                    <td>
                                        {{ $content->ayah?->surah?->id }}:
                                        {{ $content->ayah?->number }}
                                    </td>

                                    <td>
                                        {{ Str::limit($content->reflection, 100) }}
                                    </td>

                                    <td>

                                        @if ($content->is_sent)
                                            <span class="badge bg-success">
                                                Sent
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                Scheduled
                                            </span>
                                        @endif


                                    </td>
                                    <td>

                                        <form action="{{ route('admin.daily-reflections.destroy', $content) }}"
                                            method="POST" onsubmit="return confirm('Delete this reflection?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger">

                                                Delete

                                            </button>

                                        </form>

                                    </td>

                                </tr>
                            @empty

                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        No reflections created yet.
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>
                </div>

                <div class="mt-3">
                    {{ $dailyContents->links() }}
                </div>

            </div>
        </div>

    </div>
@endsection
