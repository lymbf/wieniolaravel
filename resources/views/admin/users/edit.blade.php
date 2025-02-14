@extends('layouts.app')

@section('content')
    <h1>Edycja Użytkownika</h1>

    <div style="margin-bottom:20px;">
        <a href="{{ route('admin.users.index') }}">Powrót do Listy Użytkowników</a>
    </div>

    @if($errors->any())
        <div style="color:red;">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
        @csrf
        @method('PATCH')

        <div>
            <label for="name">Nazwa:</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required>
        </div>

        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required>
        </div>

        <div>
            <label for="is_admin">Administrator:</label>
            <input type="checkbox" name="is_admin" id="is_admin" value="1" {{ $user->is_admin ? 'checked' : '' }}>
        </div>

        <div>
            <h3>Przypisz projekty</h3>
            @foreach($allProjects as $project)
                <div>
                    <input type="checkbox" name="projects[]" value="{{ $project->id }}" {{ in_array($project->id, $userProjects) ? 'checked' : '' }}>
                    <label>{{ $project->name }}</label>
                </div>
            @endforeach
        </div>

        <button type="submit">Zapisz zmiany</button>
    </form>
@endsection
