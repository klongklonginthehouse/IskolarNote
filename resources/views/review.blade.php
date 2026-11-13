@extends('layouts.app')
@section('title', 'Review queue - IskolarNotes')
@section('content')
    <div class="page-head">
        <h2>Review queue</h2>
        <p class="sub">
            {{ auth()->user()->role === 'faculty' ? 'Check practice exam solutions and approve or reject uploads.' : 'Check note accuracy, resolve copyright reports and approve or reject uploads.' }}
        </p>
    </div>

    @forelse($items as $r)
        <article class="panel queue-item">
            <div class="queue-title">
                <h3>{{ $r->title }}</h3>
                @include('partials.status')
                @if($r->open_reports > 0)
                    <span class="chip red"><x-icon name="flag" :size="12" aria-hidden="true" /> {{ $r->open_reports }} copyright report{{ $r->open_reports > 1 ? 's' : '' }}</span>
                @endif
            </div>

            <p class="meta">{{ $r->course_code }} · {{ $r->instructor }} · {{ ucfirst(str_replace('_', ' ', $r->type)) }} · by {{ $r->author->name }}</p>

            @if($r->description)
                <p class="queue-desc">{{ $r->description }}</p>
            @endif

            <div class="actions">
                @if($r->file_path)
                    <a class="btn ghost small" href="{{ route('resources.file', $r) }}"><x-icon name="eye" :size="14" aria-hidden="true" /> Open file</a>
                @endif
                <form method="POST" action="{{ route('review.approve', $r) }}">
                    @csrf
                    <button type="submit" class="btn small"><x-icon name="check-circle" :size="14" aria-hidden="true" /> {{ $r->status === 'pending' ? 'Verify' : 'Keep as verified' }}</button>
                </form>
                <form method="POST" action="{{ route('review.reject', $r) }}">
                    @csrf
                    <button type="submit" class="btn maroon small"><x-icon name="x-circle" :size="14" aria-hidden="true" /> Reject</button>
                </form>
                @if($r->open_reports > 0)
                    <form method="POST" action="{{ route('review.dismiss', $r) }}">
                        @csrf
                        <button type="submit" class="btn ghost small"><x-icon name="x" :size="14" aria-hidden="true" /> Dismiss reports</button>
                    </form>
                @endif
            </div>
        </article>
    @empty
        <div class="empty">
            <strong>The queue is clear</strong>
            New uploads and reports will show up here.
        </div>
    @endforelse
@endsection
