@extends('layouts.app')

@section('content')
    <h1>Edycja projektu</h1>

    @if($errors->any())
        <div style="color:red;">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.projects.update', $project->id) }}">
        @csrf
        @method('PATCH')

        <div>
            <label for="name">Nazwa projektu:</label>
            <input type="text" name="name" id="name" value="{{ old('name', $project->name) }}" required>
        </div>

        <div>
            <label for="description">Opis:</label>
            <textarea name="description" id="description">{{ old('description', $project->description) }}</textarea>
        </div>

        <button type="submit">Zapisz zmiany</button>
    </form>
@endsection
