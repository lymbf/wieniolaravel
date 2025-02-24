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
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $meeting->title }}</li>
        </ol>
    </nav>

    <!-- Dane spotkania -->
    <div class="card modern-card mb-4">
        <div class="card-header meetings">
            <h2 class="mb-0" style="font-size: 1.4rem;">{{ $meeting->title }}</h2>
        </div>
        <div class="card-body">
            <!-- Termin spotkania - wyeksponowany -->
            <p class="text-muted mb-2" style="font-size: 1.15rem;">
                <strong>
                    <i class="far fa-clock"></i> Termin:
                </strong>
                <span style="font-weight: 500;">
                    {{ \Carbon\Carbon::parse($meeting->date)->format('d.m.Y H:i') }}
                </span>
            </p>

            <!-- Lokalizacja i opis - również lekko większe -->
            <p class="mb-1" style="font-size: 1.1rem;">
                <strong>Lokalizacja:</strong>
                <span style="font-weight: 500;">
                    {{ $meeting->location }}
                </span>
            </p>

            @if($meeting->description)
                <p class="mb-0" style="font-size: 1.1rem;">
                    <strong>Opis:</strong>
                    <span style="font-weight: 500;">
                        {{ $meeting->description }}
                    </span>
                </p>
            @endif

            <!-- Historia zmian terminu (pełna historia) -->
            @if($meeting->histories && $meeting->histories->count())
                <div class="mt-4 p-3 border rounded bg-light">
                    <h5 class="mb-3" style="font-size: 1.2rem; font-weight: 600;">Historia zmian terminów</h5>
                    @foreach($meeting->histories->sortBy('changed_at') as $history)
                        <p style="margin-bottom: 5px; font-size: 1rem;">
                            <span style="text-decoration: line-through; color: red;">
                                {{ \Carbon\Carbon::parse($history->old_date)->format('d.m.Y H:i') }}
                            </span>
                            &rarr;
                            <span style="font-weight: bold; color: green;">
                                {{ \Carbon\Carbon::parse($history->new_date)->format('d.m.Y H:i') }}
                            </span>
                            <br>
                            <small>
                                (zmienione przez {{ $history->changedBy->name ?? $history->changed_by }} w 
                                {{ \Carbon\Carbon::parse($history->changed_at)->format('d.m.Y H:i') }})
                            </small>

                            <!-- Przycisk usuwania zmiany, jeśli chcesz go tutaj -->
                            @if(auth()->user()->isAdmin())
                                <form action="{{ route('meetings.histories.destroy', ['project' => $project->id, 'meeting' => $meeting->id, 'history' => $history->id]) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Czy na pewno chcesz usunąć tę zmianę?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger ms-2">Usuń zmianę</button>
                                </form>
                            @endif
                        </p>
                    @endforeach
                </div>
            @endif

            <!-- Załączniki do spotkania -->
            @if($meeting->attachments && $meeting->attachments->count())
                <div class="mt-4 p-3 border rounded bg-light">
                    <strong style="font-size: 1.1rem;">Załączniki:</strong>
                    <ul class="list-unstyled mb-0" style="font-size: 1rem;">
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
        </div>
        <!-- Stopka spotkania -->
        <div class="card-footer d-flex flex-column gap-2">
            <div class="d-flex justify-content-end">
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
        </div>
    </div>

    <!-- Sekcja komentarzy -->
    <div class="card shadow-sm p-4 mb-4">
        <h3 class="section-title mb-3" style="font-size: 1.5rem; font-weight: 600;">Komentarze</h3>
        @if($meeting->comments->count())
            @foreach($meeting->comments as $comment)
                <div class="card mb-3">
                    <div class="card-body">
                        <p style="font-size: 1rem; font-weight: bold; margin-bottom: 0.25rem;">
                            {{ $comment->user->name ?? 'Nieznany' }}
                        </p>
                        <small class="text-muted" style="display: block; margin-bottom: 0.5rem;">
                            {{ \Carbon\Carbon::parse($comment->created_at)->format('d.m.Y H:i') }}
                        </small>
                        <p style="font-size: 1.05rem; margin-bottom: 0.5rem;">
                            {{ $comment->content }}
                        </p>
                        <!-- Załączniki w komentarzu -->
                        @if($comment->attachments && $comment->attachments->count())
                            <div class="mt-2 p-2 border rounded bg-light">
                                <strong>Załączniki:</strong>
                                <ul class="list-unstyled mb-0" style="font-size: 0.95rem;">
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

                        <!-- Przycisk usuwania komentarza (admin lub właściciel) -->
                        @if(auth()->user()->isAdmin() || $comment->user_id === auth()->id())
    <form action="{{ route('meeting.comments.destroy', $comment->id) }}"
          method="POST"
          class="d-inline"
          onsubmit="return confirm('Czy na pewno chcesz usunąć ten komentarz?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger mt-2">
            Usuń komentarz
        </button>
    </form>
@endif

                    </div>
                </div>
            @endforeach
        @else
            <p class="text-muted" style="font-size: 1.1rem;">Brak komentarzy.</p>
        @endif

        <!-- Formularz dodania komentarza -->
        <div class="mt-4">
            <h4 class="section-title mb-3" style="font-size: 1.4rem; font-weight: 600;">Dodaj komentarz</h4>
            <form action="{{ route('meeting.comments.store', $meeting->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-2">
                    <label for="content" class="form-label" style="font-weight: 500;">Treść komentarza</label>
                    <textarea name="content" id="content" rows="3" class="form-control" style="font-size: 1.05rem;" required></textarea>
                </div>
                <div class="mb-2">
                    <label for="attachment" class="form-label" style="font-weight: 500;">Załącznik (opcjonalnie)</label>
                    <input type="file" name="attachment" id="attachment" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary" style="font-size: 1.05rem;">
                    <i class="fas fa-paper-plane"></i> Wyślij komentarz
                </button>
            </form>
        </div>
    </div>

    <!-- Powrót do listy spotkań -->
    <a href="{{ route('projects.meetings', $project->id) }}" class="btn btn-secondary">
        ⮜ Powrót do listy spotkań
    </a>
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
        font-size: 1rem;
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
        font-size: 1.05rem;
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
