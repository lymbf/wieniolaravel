@extends('layouts.app')

@section('content')
    <h1>{{ $project->name }}</h1>
    <p>{{ $project->description }}</p>

    @if(auth()->user()->projects()->count() > 1)
        <a href="{{ route('projects.index') }}">Powrót do listy projektów</a>
    @endif

    <!-- Dalsza zawartość dashboardu projektu, np. moduły (pytania, spotkania, załączniki) -->
@endsection


@section('content')
    <h1>{{ $project->name }}</h1>
    <p>{{ $project->description }}</p>

    {{-- Tu możesz dodać moduły: pytania, spotkania, załączniki --}}
    <div>
        <h2>Dashboard projektu</h2>
        <!-- Przykładowe sekcje -->
        <a href="#">Ostatnie pytania</a>
        <a href="#">Harmonogram spotkań</a>
        <a href="#">Załączniki</a>
    </div>
@endsection
