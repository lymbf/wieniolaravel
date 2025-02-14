@extends('layouts.app')

@section('content')
    <h1>Panel Administracyjny - Projekty</h1>

    <div style="margin-bottom:20px;">
        <a href="{{ route('admin.dashboard') }}">Powrót do Panelu Administracyjnego</a>
    </div>

    <a href="{{ route('admin.projects.create') }}">Dodaj nowy projekt</a>

    <!-- Tabela projektów -->
    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nazwa</th>
                <th>Opis</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projects as $project)
                <tr>
                    <td>{{ $project->id }}</td>
                    <td>{{ $project->name }}</td>
                    <td>{{ $project->description }}</td>
                    <td>
                        <a href="{{ route('admin.projects.edit', $project->id) }}">Edytuj</a>
                        <form method="POST" action="{{ route('admin.projects.destroy', $project->id) }}" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Czy na pewno chcesz usunąć ten projekt?')">Usuń</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
