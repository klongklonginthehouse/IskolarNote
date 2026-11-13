@extends('layouts.app')
@section('title', 'Administration - IskolarNotes')
@section('content')
    <div class="page-head">
        <h2>Administration</h2>
        <p class="sub">Account roles and site totals. Changing a role here is the only place role promotion happens.</p>
    </div>

    <div class="panel">
        <dl class="totals">
            <div class="stat">
                <dt>Users</dt>
                <dd>{{ $stats['users'] }}</dd>
            </div>
            <div class="stat">
                <dt>Resources</dt>
                <dd>{{ $stats['resources'] }}</dd>
            </div>
            <div class="stat">
                <dt>Awaiting review</dt>
                <dd>{{ $stats['pending'] }}</dd>
            </div>
            <div class="stat">
                <dt>Open reports</dt>
                <dd>{{ $stats['reports'] }}</dd>
            </div>
        </dl>

        <div class="section-head">
            <h3>Accounts per role</h3>
        </div>
        <div class="role-list">
            @foreach($roles as $role)
                <span class="chip">{{ $role }}: {{ $counts[$role] ?? 0 }}</span>
            @endforeach
        </div>
    </div>

    <div class="panel">
        <div class="section-head">
            <h3>Users</h3>
            <span class="meta">{{ $users->total() }} total</span>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Role</th>
                    <th scope="col">Change role</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $u)
                    <tr>
                        <td data-label="ID">{{ $u->id }}</td>
                        <td data-label="Name">{{ $u->name }}</td>
                        <td data-label="Email">{{ $u->email }}</td>
                        <td data-label="Role"><span class="chip {{ $u->role === 'admin' ? 'green' : '' }}">{{ $u->role }}</span></td>
                        <td data-label="">
                            @if($u->id === auth()->id())
                                <span class="meta m-0">This is you</span>
                            @else
                                <form method="POST" action="{{ route('admin.users.role', $u) }}" class="actions">
                                    @csrf
                                    <select name="role" aria-label="Role for {{ $u->name }}">
                                        @foreach($roles as $role)
                                            <option value="{{ $role }}" @selected($u->role === $role)>{{ $role }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn small">Save</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="pager">{{ $users->links() }}</div>
    </div>
@endsection
