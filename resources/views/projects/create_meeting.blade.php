@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Dodaj nowe spotkanie</h1>
        <a href="{{ route('projects.meetings', $project->id) }}" class="btn btn-secondary">Powrót do spotkań</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('meetings.store', ['project' => $project->id]) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label">Temat spotkania</label>
            <input type="text" name="title" id="title" class="form-control" placeholder="Wpisz temat spotkania" required>
        </div>
        
        <div class="mb-3">
            <label for="description" class="form-label">Opis spotkania</label>
            <textarea name="description" id="description" class="form-control" rows="4" placeholder="Wpisz opis spotkania" required></textarea>
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Data i godzina spotkania</label>
            <input type="datetime-local" name="date" id="date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Typ spotkania</label>
            <select name="type" id="type" class="form-select" required>
                <option value="on_site">Na budowie</option>
                <option value="online">Online</option>
                <option value="custom">Własna lokalizacja</option>
            </select>
        </div>

        <div class="mb-3" id="custom-location" style="display: none;">
            <label for="location" class="form-label">Podaj własną lokalizację</label>
            <input type="text" name="location" id="location" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Dodaj spotkanie</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function(){
        const typeSelect = document.getElementById('type');
        const customLocation = document.getElementById('custom-location');

        typeSelect.addEventListener('change', function() {
            customLocation.style.display = this.value === 'custom' ? 'block' : 'none';
        });
    });
</script>
@endsection
