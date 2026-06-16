@extends('admin.layout')
@section('title', 'Edit Prophet')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('admin.prophets.index') }}" class="btn btn-sm btn-light">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h5 class="mb-0" style="font-family:var(--font-heading)">
                        {{-- ✅ Fixed --}}
                        {{ $prophet->display_name }}
                    </h5>
                    <small class="text-muted">Prophet #{{ $prophet->order }} of 25</small>
                </div>
            </div>

            {{-- Read-only info --}}
            <div class="admin-table p-4 mb-4" style="background:rgba(27,94,59,0.04)">

                <div class="d-flex justify-content-between align-items-center">

                    {{-- Left: English name + honour title --}}
                    <div>
                        <h3 style="font-family:var(--font-heading); font-size:1.8rem; margin:0">
                            {{-- ✅ Fixed --}}
                            {{ $prophet->display_name }}
                        </h3>
                        <p class="text-muted mb-2">{{ $prophet->name_english }}</p>

                        {{-- English honour title --}}
                        @if ($prophet->title)
                            <span class="badge"
                                style="background:rgba(27,94,59,0.1);
                                         color:var(--emerald);
                                         font-size:0.8rem;
                                         padding:0.4rem 0.8rem">
                                {{ $prophet->title }}
                            </span>
                        @endif
                    </div>

                    {{-- Right: Arabic name + Arabic honour title --}}
                    <div style="text-align:right">

                        {{-- Arabic name --}}
                        <p
                            style="font-family:'Amiri', serif;
                                  font-size:2.8rem;
                                  direction:rtl;
                                  text-align:right;
                                  unicode-bidi:bidi-override;
                                  color:var(--emerald);
                                  line-height:1.4;
                                  margin:0">
                            {{ $prophet->name_arabic }}
                        </p>

                        {{-- Arabic honour title --}}
                        @if ($prophet->title_arabic)
                            <p
                                style="font-family:'Amiri', serif;
                                      font-size:1.3rem;
                                      direction:rtl;
                                      text-align:right;
                                      unicode-bidi:bidi-override;
                                      color:var(--gold);
                                      margin:0;
                                      line-height:1.8">
                                {{ $prophet->title_arabic }}
                            </p>
                            <small
                                style="display:block;
                                          text-align:right;
                                          color:#999;
                                          font-size:0.75rem">
                                {{ $prophet->title_transliteration }}
                            </small>
                        @endif

                    </div>
                </div>

                {{-- Quran references --}}
                @if ($prophet->mentioned_in_quran)
                    <hr style="border-color:var(--border)" class="my-3">
                    <small class="text-muted">
                        <i class="bi bi-book me-1"></i>
                        {{ $prophet->mentioned_in_quran }}
                    </small>
                @endif

            </div>

            {{-- Editable info --}}
            <div class="admin-table p-4">
                <h6 class="heading-font mb-3">Edit Details</h6>

                <form action="{{ route('admin.prophets.update', $prophet) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Title / Epithet (English)</label>
                            <input type="text" name="title" class="form-control"
                                value="{{ old('title', $prophet->title) }}" placeholder="e.g. Friend of Allah">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Title Transliteration</label>
                            <input type="text" name="title_transliteration" class="form-control"
                                value="{{ old('title_transliteration', $prophet->title_transliteration) }}"
                                placeholder="e.g. Khalilullah">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium">Arabic Honour Title</label>
                            <input type="text" name="title_arabic" class="form-control"
                                value="{{ old('title_arabic', $prophet->title_arabic) }}" placeholder="e.g. خَلِيلُ اللَّه"
                                style="font-family:'Amiri',serif;
                                          font-size:1.2rem;
                                          direction:rtl;
                                          text-align:right">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium">Period</label>
                            <input type="text" name="period" class="form-control"
                                value="{{ old('period', $prophet->period) }}" placeholder="e.g. circa 1800 BCE">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium">Quran References</label>
                            <input type="text" name="mentioned_in_quran" class="form-control"
                                value="{{ old('mentioned_in_quran', $prophet->mentioned_in_quran) }}"
                                placeholder="e.g. Al-Baqarah 2:124-132">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium">Summary *</label>
                            <textarea name="summary" class="form-control" rows="6" required>{{ old('summary', $prophet->summary) }}</textarea>
                            <small class="text-muted">
                                Shown on the prophet's profile page.
                            </small>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-admin-primary">
                                <i class="bi bi-check-lg me-1"></i>Save Changes
                            </button>
                        </div>

                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection
