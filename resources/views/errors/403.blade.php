@extends('layouts.app')
@section('title', '403 - Access denied')
@section('content')
    <div class="panel">
        <h2>403 — Access denied</h2>
        <p class="sub">Your account role does not permit this page or action.</p>
        <p class="meta">Signed in as {{ auth()->user()?->name ?? 'guest' }}
            @if(auth()->check()) (role: {{ auth()->user()->role }}) @endif</p>
        <p class="meta">Requested: {{ url()->current() }}</p>
        <div class="actions">
            <a class="btn ghost" href="{{ url('/browse') }}">Back to browse</a>
            @if(auth()->check())
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn small maroon" type="submit">Log out</button>
                </form>
            @else
                <a class="btn" href="{{ route('login') }}">Log in</a>
            @endif
        </div>
    </div>
@endsection
