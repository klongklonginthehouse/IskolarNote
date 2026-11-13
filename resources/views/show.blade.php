@extends('layouts.app')
@section('title', $r->title.' - IskolarNotes')
@section('content')
    @php $free = $r->owned || $r->user_id === auth()->id() || $r->cost === 0; @endphp

    <p><a class="btn ghost small" href="{{ route('browse') }}"><x-icon name="arrow-left" :size="14" aria-hidden="true" /> Back to browse</a></p>

    <article class="panel detail">
        <div class="detail-head">
            <span class="chip"><x-icon name="file-text" :size="12" aria-hidden="true" /> {{ ucfirst(str_replace('_', ' ', $r->type)) }}</span>
            <span class="chip">{{ $r->course_code }}</span>
            @include('partials.status')
        </div>

        <h2>{{ $r->title }}</h2>
        <p class="meta">{{ $r->instructor }} · {{ $r->department->name }}</p>

        <p class="resource-description">{{ $r->description ?: 'No description provided.' }}</p>

        <dl class="stats">
            <div class="stat">
                <dt>Upvotes</dt>
                <dd>{{ $r->votes_count }}</dd>
            </div>
            <div class="stat">
                <dt>Downloads</dt>
                <dd>{{ $r->downloads_count }}</dd>
            </div>
            <div class="stat">
                <dt>Uploaded</dt>
                <dd>{{ $r->created_at->diffForHumans() }}</dd>
            </div>
            <div class="stat">
                <dt>By</dt>
                <dd>{{ $r->author->name }}</dd>
            </div>
        </dl>

        @if($r->status === 'verified')
            <div class="detail-actions">
                <form method="POST" action="{{ route('resources.download', $r) }}">
                    @csrf
                    <button type="submit" class="btn">
                        <x-icon name="download" :size="16" aria-hidden="true" />
                        {{ $free ? 'Download' : 'Download for '.$r->cost.' credits' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('resources.vote', $r) }}">
                    @csrf
                    <button type="submit" class="btn ghost vote {{ $r->voted ? 'on' : '' }}" aria-pressed="{{ $r->voted ? 'true' : 'false' }}">
                        <x-icon name="thumbs-up" :size="16" aria-hidden="true" /> {{ $r->voted ? 'Upvoted' : 'Upvote' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('resources.report', $r) }}" onsubmit="return confirm('Send this resource to the moderators for review?')">
                    @csrf
                    <button type="submit" class="linkbtn"><x-icon name="flag" :size="13" aria-hidden="true" /> Report copyright issue</button>
                </form>
            </div>
        @else
            <p class="meta mt-5">This resource is not public yet.</p>
        @endif
    </article>
@endsection
