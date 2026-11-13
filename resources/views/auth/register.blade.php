@extends('layouts.auth')
@section('title', 'Create account - IskolarNotes')
@section('hero-title')Join <x-wordmark />@endsection

@section('hero')
    <p>You start with 10 credits. Downloading costs credits, and every note you share that gets verified earns you 5 more.</p>
    <ul>
        <li><x-icon name="check-circle" :size="18" aria-hidden="true" /> Browse every department without spending a credit</li>
        <li><x-icon name="check-circle" :size="18" aria-hidden="true" /> Study accounts read; author accounts also upload</li>
        <li><x-icon name="check-circle" :size="18" aria-hidden="true" /> A moderator checks each upload before it goes public</li>
    </ul>
@endsection

@section('form-title', 'Create account')
@section('form')
    <form method="POST" action="{{ url('/register') }}">
        @csrf
        <div class="field">
            <label for="name">Full name</label>
            <input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required>
        </div>
        <div class="field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="new-password" required>
            <p class="field-hint">At least 8 characters.</p>
        </div>
        <div class="field">
            <label for="role">I want to</label>
            <select id="role" name="role">
                <option value="student" @selected(old('role') === 'student')>Study from notes</option>
                <option value="author" @selected(old('role') === 'author')>Upload and study</option>
            </select>
        </div>
        @if($errors->any())
            <ul class="errors">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        @endif
        <div class="form-actions">
            <button class="btn maroon" type="submit"><x-icon name="user-plus" :size="16" aria-hidden="true" /> Create account</button>
        </div>
    </form>
@endsection

@section('alt')
    Already registered? <a href="{{ route('login') }}">Log in</a>
@endsection
