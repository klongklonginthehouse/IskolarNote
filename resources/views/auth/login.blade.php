@extends('layouts.auth')
@section('title', 'Log in - IskolarNotes')
@section('hero-title')<x-wordmark />@endsection

@section('hero')
    <p>A shared library of lecture notes, reviewers and past exams, organized by department, course and instructor.</p>
    <ul>
        <li><x-icon name="check-circle" :size="18" aria-hidden="true" /> Find notes for your exact course in seconds</li>
        <li><x-icon name="check-circle" :size="18" aria-hidden="true" /> Practice with past exams checked by moderators and faculty</li>
        <li><x-icon name="check-circle" :size="18" aria-hidden="true" /> Earn credits and badges when you share your own notes</li>
    </ul>
    <p class="demo">Demo accounts (password <b>password123</b>): admin@iskolar.test, student@iskolar.test, author@iskolar.test, moderator@iskolar.test, faculty@iskolar.test</p>
@endsection

@section('form-title', 'Log in')
@section('form')
    <form method="POST" action="{{ url('/login') }}">
        @csrf
        <div class="field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
        </div>
        @if($errors->any())
            <ul class="errors">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        @endif
        <div class="form-actions">
            <button class="btn maroon" type="submit"><x-icon name="log-in" :size="16" aria-hidden="true" /> Log in</button>
        </div>
    </form>
@endsection

@section('alt')
    New here? <a href="{{ route('register') }}">Create an account</a>
@endsection
