@extends('layouts.app')
@section('title', 'Administration - IskolarNotes')
@section('content')
    <h2>Administration</h2>
    <p class="sub">Account roles and site totals. Changing a role here is the only place role promotion happens.</p>

    <div class="grid2" style="margin-bottom:16px">
        <div class="panel">
            <h3 style="margin-top:0">Totals</h3>
            <p class="meta" style="margin-bottom:8px">Users <b>{{ $stats['users'] }}</b></p>
            <p class="meta" style="margin-bottom:8px">Resources <b>{{ $stats['resources'] }}</b></p>
            <p class="meta" style="margin-bottom:8px">Awaiting review <b>{{ $stats['pending'] }}</b></p>
            <p class="meta" style="margin-bottom:0">Open copyright reports <b>{{ $stats['reports'] }}</b></p>
        </div>
        <div class="panel">
            <h3 style="margin-top:0">Accounts per role</h3>
            <div class="actions">
                @foreach($roles as $role)
                    <span class="chip">{{ $role }}: {{ $counts[$role] ?? 0 }}</span>
                @endforeach
            </div>
        </div>
    </div>

    <div class="panel">
        <h3 style="margin-top:0">Users</h3>
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Change role</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $u)
                <tr>
                    <td>{{ $u->id }}</td>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td><span class="chip {{ $u->role === 'admin' ? 'green' : '' }}">{{ $u->role }}</span></td>
                    <td>
                        @if($u->id === auth()->id())
                            <span class="meta" style="margin:0">This is you</span>
                        @else
                            <form method="POST" action="{{ route('admin.users.role', $u) }}" class="actions">
                                @csrf
                                <select name="role" aria-label="Role for {{ $u->name }}" style="width:auto">
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

        <div class="pager">{{ $users->links() }}</div>
    </div>
@endsection
