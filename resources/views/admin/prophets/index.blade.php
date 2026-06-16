@extends('admin.layout')
@section('title', 'Prophets')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0" style="font-family:var(--font-heading)">
            All 25 Prophets
        </h5>
        <small class="text-muted">Click Edit to update prophet info</small>
    </div>

    <div class="admin-table">
        <table class="table table-hover" style="table-layout:fixed; width:100%">
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th style="width:200px">Prophet</th>
                    <th style="width:150px; text-align:right">Arabic & Title</th>
                    <th style="width:180px">Period</th>
                    <th style="width:100px">Stories</th>
                    <th style="width:80px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prophets as $prophet)
                    <tr>
                        {{-- Number --}}
                        <td style="color:#999; font-size:0.85rem; vertical-align:middle">
                            {{ $prophet->order }}
                        </td>

                        {{-- Prophet Name + English Title --}}
                        <td style="vertical-align:middle">
                            <div style="font-size:0.9rem; font-weight:500">
                                {{-- ✅ Uses model accessor --}}
                                {{ $prophet->display_name }}
                            </div>
                            <small class="text-muted">{{ $prophet->title }}</small>
                        </td>

                        {{-- Arabic Name + Honour Title --}}
                        <td style="text-align:right; vertical-align:middle">

                            {{-- Arabic name --}}
                            <span
                                style="font-family:'Amiri', serif;
                                     font-size:1.6rem;
                                     direction:rtl;
                                     display:block;
                                     text-align:right;
                                     unicode-bidi:bidi-override;
                                     color:var(--emerald);
                                     line-height:1.8; margin-bottom:0.2rem">
                                {{ $prophet->name_arabic }}
                            </span>

                            {{-- Arabic honour title --}}
                            @if ($prophet->title_arabic)
                                <span
                                    style="font-family:'Amiri', serif;
                                         font-size:1rem;
                                         direction:rtl;
                                         display:block;
                                         text-align:right;
                                         unicode-bidi:bidi-override;
                                         color:var(--gold);
                                         line-height:1.6">
                                    {{ $prophet->title_arabic }}
                                </span>
                                <small
                                    style="display:block;
                                          text-align:right;
                                          color:#999;
                                          font-size:0.7rem">
                                    {{ $prophet->title_transliteration }}
                                </small>
                            @endif

                        </td>

                        {{-- Period --}}
                        <td style="font-size:0.82rem; color:#666; vertical-align:middle">
                            {{ $prophet->period ?? '—' }}
                        </td>

                        {{-- Stories --}}
                        <td style="vertical-align:middle">
                            <span class="badge bg-secondary">
                                {{ $prophet->stories_count }} stories
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td style="vertical-align:middle">
                            <a href="{{ route('admin.prophets.edit', $prophet) }}" class="btn btn-sm btn-admin-primary">
                                Edit
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection
