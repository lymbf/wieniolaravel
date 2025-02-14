@extends('layouts.app')

@section('content')
    <h1>Panel Administracyjny - Użytkownicy</h1>

    <div style="margin-bottom:20px;">
        <a href="{{ route('admin.dashboard') }}">Powrót do Panelu Administracyjnego</a>
    </div>

    @if(session('status'))
        <p style="color:green;">{{ session('status') }}</p>
    @endif

    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nazwa</th>
                <th>Email</th>
                <th>Projekty</th>
                <th>Dostęp</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->projects->isNotEmpty())
                            <ul style="padding-left:15px; margin:0;">
                                @foreach($user->projects as $project)
                                    <li>{{ $project->name }}</li>
                                @endforeach
                            </ul>
                        @else
                            Brak
                        @endif
                    </td>
                    <td>{{ $user->is_admin ? 'Administrator' : 'Użytkownik' }}</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user->id) }}">Edytuj</a>
                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Czy na pewno chcesz usunąć tego użytkownika?')">Usuń</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
