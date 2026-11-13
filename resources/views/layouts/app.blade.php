<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>@yield('title', 'IskolarNotes')</title>
    <link rel="stylesheet" href="{{ asset('css/iskolar.css') }}">
</head>
<body>
@php $me = auth()->user(); @endphp
<a class="skip" href="#main">Skip to content</a>

<header class="top">
    <div class="wrap bar">
        <a class="brand" href="{{ route('browse') }}">
            <h1><x-wordmark /></h1>
            <p class="header-tagline">Notes and past exams, shared by students, checked by students.</p>
        </a>
        <div class="who">
            <span class="chip"><x-icon name="coins" :size="14" aria-hidden="true" /> {{ $me->credits }} credits</span>
            <span class="chip" data-role><x-icon name="user" :size="14" aria-hidden="true" /> {{ $me->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="linkbtn" type="submit"><x-icon name="log-out" :size="14" aria-hidden="true" /> Log out</button>
            </form>
        </div>
    </div>
    <nav class="wrap nav" aria-label="Main">
        <a href="{{ route('browse') }}" class="{{ request()->routeIs('browse', 'resources.show') ? 'on' : '' }}" @if(request()->routeIs('browse', 'resources.show')) aria-current="page" @endif><x-icon name="book-open" :size="16" aria-hidden="true" /> Browse</a>
        @if($me->canUpload())
            <a href="{{ route('upload') }}" class="{{ request()->routeIs('upload') ? 'on' : '' }}" @if(request()->routeIs('upload')) aria-current="page" @endif><x-icon name="upload" :size="16" aria-hidden="true" /> {{ $me->role === 'faculty' ? 'Publish official' : 'Upload' }}</a>
        @endif
        @if($me->isStaff())
            <a href="{{ route('review') }}" class="{{ request()->routeIs('review') ? 'on' : '' }}" @if(request()->routeIs('review')) aria-current="page" @endif><x-icon name="shield-check" :size="16" aria-hidden="true" /> Review queue</a>
        @endif
        @if($me->isAdmin())
            <a href="{{ route('admin') }}" class="{{ request()->routeIs('admin') ? 'on' : '' }}" @if(request()->routeIs('admin')) aria-current="page" @endif><x-icon name="user" :size="16" aria-hidden="true" /> Administration</a>
        @endif
        <a href="{{ route('shelf') }}" class="{{ request()->routeIs('shelf') ? 'on' : '' }}" @if(request()->routeIs('shelf')) aria-current="page" @endif><x-icon name="file-text" :size="16" aria-hidden="true" /> My shelf</a>
    </nav>
</header>

<main class="wrap" id="main">
    @if(session('status'))<div class="flash ok" role="status">{{ session('status') }}</div>@endif
    @if(session('error'))<div class="flash err" role="alert">{{ session('error') }}</div>@endif
    @yield('content')
</main>

@if(session('download_url'))
    <iframe src="{{ session('download_url') }}" hidden title="Download"></iframe>
@endif
</body>
</html>
