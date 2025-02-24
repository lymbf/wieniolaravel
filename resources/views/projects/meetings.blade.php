@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-light p-2 rounded shadow-sm">
        <li class="breadcrumb-item">
            <a href="{{ route('projects.index') }}" class="text-decoration-none">
                <strong>Lista Projektów</strong>
            </a>
        </li>
        <li class="breadcrumb-item">
        <a href="{{ route('projects.show', $project->id) }}" class="text-decoration-none">
    Pulpit
</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('projects.meetings', $project->id) }}" class="text-decoration-none">
                Harmonogram spotkań
            </a>
    </ol>
</nav>


    <!-- Nagłówek strony -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="project-title">Spotkania dla projektu: {{ $project->name }}</h1>
        <a href="{{ route('meetings.create', ['project' => $project->id]) }}" class="btn btn-custom shadow-lg">
            <i class="fas fa-calendar-plus"></i> Dodaj nowe spotkanie
        </a>
    </div>

    @if($meetings->isEmpty())
        <div class="alert alert-info text-center shadow-sm">
            Brak spotkań w tym projekcie.
        </div>
    @else
        <!-- Lista spotkań w pojedynczej kolumnie -->
        <div class="d-flex flex-column gap-4">
            @foreach($meetings as $meeting)
                <div class="card modern-card">
                    <!-- Nagłówek spotkania -->
                    <div class="card-header meetings">
                        <h5 class="mb-0">{{ $meeting->title }}</h5>
                    </div>
                    <!-- Treść spotkania -->
                    <div class="card-body">
                        <p class="text-muted mb-2">
                            <i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($meeting->date)->format('d.m.Y H:i') }}
                        </p>
                        <p class="mb-1"><strong>Lokalizacja:</strong> {{ $meeting->location }}</p>
                        @if($meeting->description)
                            <p class="mb-0"><strong>Opis:</strong> {{ \Illuminate\Support\Str::limit($meeting->description, 100) }}</p>
                        @endif

                        <!-- Sekcja historii zmiany terminu (jeśli istnieje) -->
                        @if($meeting->histories && $meeting->histories->count())
                            @php
                                $lastHistory = $meeting->histories->sortBy('changed_at')->last();
                            @endphp
                            <p class="mt-2">
                                <strong>Termin:</strong>
                                <span style="text-decoration: line-through; color: red;">
                                    {{ \Carbon\Carbon::parse($lastHistory->old_date)->format('d.m.Y H:i') }}
                                </span>
                                &rarr;
                                <span style="font-weight: bold; color: green;">
                                    {{ \Carbon\Carbon::parse($lastHistory->new_date)->format('d.m.Y H:i') }}
                                </span>
                                <br>
                                <small>
                                    (zmienione przez {{ $lastHistory->changedBy->name ?? $lastHistory->changed_by }} w 
                                    {{ \Carbon\Carbon::parse($lastHistory->changed_at)->format('d.m.Y H:i') }})
                                </small>
                            </p>
                        @endif

                        <!-- Załączniki do spotkania -->
                        @if($meeting->attachments && $meeting->attachments->count())
                            <div class="mt-3 p-2 border rounded bg-light">
                                <strong>Załączniki:</strong>
                                <ul class="list-unstyled mb-0">
                                    @foreach($meeting->attachments as $attach)
                                        <li>
                                            <a href="{{ asset('storage/'.$attach->file_path) }}" target="_blank">
                                                {{ $attach->original_name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Komentarze pod spotkaniem -->
                        @if($meeting->comments && $meeting->comments->isNotEmpty())
                            <div class="comments-section mt-3">
                                <h6 class="mb-2">Komentarze ({{ $meeting->comments->count() }})</h6>
                                @foreach($meeting->comments as $comment)
                                    <div class="card comment-card mb-2 p-2">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span>
                                                <i class="fas fa-user"></i> {{ $comment->user->name ?? 'Nieznany' }}
                                            </span>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($comment->created_at)->format('d.m.Y H:i') }}
                                            </small>
                                        </div>
                                        <p class="comment-text mb-0">
                                            {{ \Illuminate\Support\Str::limit($comment->content, 100) }}
                                        </p>
                                        <!-- Załączniki w komentarzu -->
                                        @if($comment->attachments && $comment->attachments->count())
                                            <div class="mt-2 p-2 border rounded bg-light">
                                                <strong>Załączniki w komentarzu:</strong>
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($comment->attachments as $cattach)
                                                        <li>
                                                            <a href="{{ asset('storage/'.$cattach->file_path) }}" target="_blank">
                                                                {{ $cattach->original_name }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <!-- Stopka spotkania z przyciskami -->
                    <div class="card-footer d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('projects.meetings.show', ['project' => $project->id, 'meeting' => $meeting->id]) }}"
                               class="btn btn-custom">
                                Szczegóły spotkania <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                            <div>
                                <!-- Przycisk do przełączania widoczności formularza edycji terminu -->
                                <button type="button" class="btn btn-sm btn-outline-secondary me-2" onclick="toggleEditForm({{ $meeting->id }})">
                                    Zmień termin
                                </button>
                                @if(auth()->user()->isAdmin() || $meeting->user_id === auth()->id())
                                    <form action="{{ route('meetings.destroy', ['project' => $project->id, 'meeting' => $meeting->id]) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Czy na pewno chcesz usunąć to spotkanie?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Usuń</button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- Inline formularz edycji terminu -->
                        <div id="editDateForm-{{ $meeting->id }}" style="display: none;" class="mt-2">
                            <div class="card card-body">
                                <form action="{{ route('meetings.updateDate', ['project' => $project->id, 'meeting' => $meeting->id]) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="mb-3">
                                        <label for="new_date_{{ $meeting->id }}" class="form-label">Nowy termin (data i godzina):</label>
                                        <input type="datetime-local" name="new_date" id="new_date_{{ $meeting->id }}" class="form-control" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">Zapisz nowy termin</button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="toggleEditForm({{ $meeting->id }})">Anuluj</button>
                                </form>
                            </div>
                        </div>

                        <!-- Formularz dodania komentarza -->
                        <div class="mt-2">
                            <form action="{{ route('meeting.comments.store', ['meeting' => $meeting->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="input-group mb-2">
                                    <input type="text" name="content" class="form-control" placeholder="Dodaj komentarz..." required>
                                </div>
                                <div class="mb-2">
                                    <label for="attachment-{{ $meeting->id }}" class="form-label">Załącznik (opcjonalnie):</label>
                                    <input type="file" name="attachment" id="attachment-{{ $meeting->id }}" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-paper-plane"></i> Wyślij komentarz
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');
    body {
        font-family: 'Roboto', sans-serif;
    }
    .project-title {
        font-size: 1.2rem;
        font-weight: bold;
        background-color: #F1C40F;
        color: #000;
        padding: 6px 12px;
        border-radius: 5px;
    }
    .modern-card {
        border: none;
        border-radius: 10px;
        background-color: #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .modern-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }
    .modern-card .card-header {
        padding: 1rem 1.25rem;
        font-weight: 700;
        font-size: 1.1rem;
        background: linear-gradient(135deg, #444, #666);
        color: #F1C40F;
    }
    .modern-card .card-body {
        padding: 1.5rem;
        font-size: 0.95rem;
    }
    .modern-card .card-footer {
        background-color: #f8f9fa;
        padding: 1rem;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }
    .comments-section {
        margin-top: 10px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
    }
    .comment-card {
        background: #fff;
        padding: 8px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.08);
        margin-bottom: 5px;
    }
    .comment-text {
        font-size: 0.95rem;
        color: #555;
    }
    .btn-custom {
        border-radius: 25px;
        font-weight: 500;
        background-color: #F1C40F;
        color: #2d2d2d;
        padding: 0.5rem 1rem;
        transition: background-color 0.3s ease, color 0.3s ease;
        border: none;
    }
    .btn-custom:hover {
        opacity: 0.9;
    }
    .breadcrumb {
        font-size: 1rem;
    }
    .input-group .form-control {
        border-radius: 0.25rem 0 0 0.25rem;
    }
    .input-group .btn {
        border-radius: 0 0.25rem 0.25rem 0;
    }
</style>
@endpush

@push('scripts')
<script>
    function toggleEditForm(meetingId) {
        var form = document.getElementById('editDateForm-' + meetingId);
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }
    var projectId = {{ $project->id }};
</script>
@endpush
