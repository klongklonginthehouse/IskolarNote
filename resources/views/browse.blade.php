@extends('layouts.app')
@section('title', 'Browse - IskolarNotes')
@section('content')
    @php
        $filtered = request()->filled('q') || request()->filled('department_id') || request()->filled('type');
    @endphp

    <div class="page-head">
        <h2>Browse resources</h2>
        <p class="sub">Search verified notes, reviewers and past exams.</p>
    </div>

    <form method="GET" action="{{ route('browse') }}" class="filters" role="search">
        <div>
            <label for="q">Search</label>
            <div class="field-icon">
                <x-icon name="search" :size="16" aria-hidden="true" />
                <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Title, course code or instructor">
            </div>
        </div>
        <div>
            <label for="department_id">Department</label>
            <select id="department_id" name="department_id">
                <option value="">All departments</option>
                @foreach($departments as $d)
                    <option value="{{ $d->id }}" @selected(request('department_id') == $d->id)>{{ $d->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="type">Type</label>
            <select id="type" name="type">
                <option value="">All types</option>
                @foreach($types as $t)
                    <option value="{{ $t }}" @selected(request('type') === $t)>{{ ucfirst(str_replace('_', ' ', $t)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="sort">Sort</label>
            <select id="sort" name="sort">
                <option value="top" @selected(request('sort', 'top') === 'top')>Most upvoted</option>
                <option value="new" @selected(request('sort') === 'new')>Newest</option>
            </select>
        </div>
        <button class="btn maroon" type="submit"><x-icon name="search" :size="16" aria-hidden="true" /> Search</button>
    </form>

    <div class="filter-foot">
        <p class="result-count">
            @if($items->total() === 0)
                No resources match.
            @elseif($items->total() === 1)
                1 resource
            @else
                {{ $items->total() }} resources
            @endif
        </p>
        @if($filtered)
            <a class="linkbtn" href="{{ route('browse') }}"><x-icon name="x" :size="14" aria-hidden="true" /> Clear filters</a>
        @endif
    </div>

    <div class="rows">
        @forelse($items as $r)
            @php $free = $r->owned || $r->user_id === auth()->id() || $r->cost === 0; @endphp
            <article class="row">
                <div class="code">
                    <b>{{ $r->course_code }}</b>
                    <span>{{ ucfirst(str_replace('_', ' ', $r->type)) }}</span>
                </div>
                <div class="body">
                    <h3><a href="{{ route('resources.show', $r) }}">{{ $r->title }}</a></h3>
                    <p class="meta">{{ $r->instructor }} · {{ $r->department->name }} · by {{ $r->author->name }}</p>
                    <div class="actions">
                        @include('partials.status')
                        <form method="POST" action="{{ route('resources.vote', $r) }}">
                            @csrf
                            <button type="submit" class="btn ghost small vote {{ $r->voted ? 'on' : '' }}" aria-pressed="{{ $r->voted ? 'true' : 'false' }}" title="Upvote">
                                <x-icon name="thumbs-up" :size="14" aria-hidden="true" /> {{ $r->votes_count }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('resources.download', $r) }}">
                            @csrf
                            <button type="submit" class="btn small">
                                <x-icon name="download" :size="14" aria-hidden="true" />
                                {{ $free ? 'Download' : 'Download for '.$r->cost.' credits' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('resources.report', $r) }}" onsubmit="return confirm('Send this resource to the moderators for review?')">
                            @csrf
                            <button type="submit" class="linkbtn"><x-icon name="flag" :size="13" aria-hidden="true" /> Report copyright issue</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="empty">
                <strong>Nothing matches those filters</strong>
                @if($filtered)
                    Try a shorter search term, or pick a different department.
                @else
                    The library is empty right now. Check back once uploads are verified.
                @endif
                @if($filtered)
                    <p><a class="btn ghost" href="{{ route('browse') }}">Clear filters</a></p>
                @endif
            </div>
        @endforelse
    </div>

    @if($items->hasPages())
        <nav class="pager" aria-label="Pagination">
            @if($items->previousPageUrl())<a class="btn ghost small" href="{{ $items->previousPageUrl() }}" rel="prev"><x-icon name="chevron-left" :size="14" aria-hidden="true" /> Previous</a>@endif
            <span>Page {{ $items->currentPage() }} of {{ $items->lastPage() }}</span>
            @if($items->nextPageUrl())<a class="btn ghost small" href="{{ $items->nextPageUrl() }}" rel="next">Next <x-icon name="chevron-right" :size="14" aria-hidden="true" /></a>@endif
        </nav>
    @endif
@endsection
