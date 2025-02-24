@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Zadaj nowe pytanie dla projektu: {{ $project->name }}</h2>
    <form action="{{ route('projects.questions.store', $project->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="project_id" value="{{ $project->id }}">
        <div class="mb-3">
            <label for="title" class="form-label">Tytuł pytania</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Wpisz tytuł pytania" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Opis pytania</label>
            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Opisz szczegóły pytania" required></textarea>
        </div>
        <div class="mb-3">
            <label for="attachment" class="form-label">Załącznik (opcjonalnie)</label>
            <input type="file" class="form-control" id="attachment" name="attachment">
        </div>
        <button type="submit" class="btn btn-primary">Wyślij pytanie</button>
    </form>
</div>
@endsection
