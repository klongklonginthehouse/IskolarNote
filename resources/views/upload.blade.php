@extends('layouts.app')
@section('title', 'Upload - IskolarNotes')
@section('content')
    @php $faculty = auth()->user()->role === 'faculty'; @endphp

    <div class="page-head">
        <h2>{{ $faculty ? 'Publish official material' : 'Upload a resource' }}</h2>
        <p class="sub">
            {{ $faculty ? 'Faculty uploads are marked Official and appear in search right away.' : 'Moderators check uploads before they appear in search. You earn 5 credits when yours is verified.' }}
        </p>
    </div>

    <form class="panel form-panel" method="POST" action="{{ route('upload.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="field">
            <label for="title">Title</label>
            <input id="title" name="title" value="{{ old('title') }}" required>
        </div>

        <div class="field-row">
            <div class="field">
                <label for="course_code">Course code</label>
                <input id="course_code" name="course_code" value="{{ old('course_code') }}" placeholder="BSICT" required>
            </div>
            <div class="field">
                <label for="instructor">Instructor</label>
                <input id="instructor" name="instructor" value="{{ old('instructor') }}" required>
            </div>
        </div>

        <div class="field-row">
            <div class="field">
                <label for="department_id">Department</label>
                <select id="department_id" name="department_id">
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" @selected(old('department_id') == $d->id)>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="type">Type</label>
                <select id="type" name="type">
                    @foreach($types as $t)
                        @if($t !== 'syllabus' || $faculty)
                            <option value="{{ $t }}" @selected(old('type', $faculty ? 'syllabus' : 'lecture_notes') === $t)>{{ ucfirst(str_replace('_', ' ', $t)) }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>

        <div class="field">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3">{{ old('description') }}</textarea>
            <p class="field-hint">Optional. Say what the file covers and which topics it is good for.</p>
        </div>

        <div class="field">
            <label for="file">File</label>
            <input id="file" name="file" type="file" required>
            <p class="field-hint">PDF, Word, slides, image or text, up to 20 MB.</p>
        </div>

        @if($errors->any())
            <ul class="errors">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        @endif

        <div class="form-actions">
            <button class="btn maroon" type="submit"><x-icon name="upload" :size="16" aria-hidden="true" /> {{ $faculty ? 'Publish' : 'Upload' }}</button>
        </div>
    </form>
@endsection
