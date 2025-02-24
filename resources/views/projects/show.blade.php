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
            <li class="breadcrumb-item active" aria-current="page">{{ $project->name }}</li>
        </ol>
    </nav>
    
    <!-- Przycisk wylogowania -->
    <form action="{{ route('logout') }}" method="POST" class="mb-4 text-end">
        @csrf
        <button type="submit" class="btn btn-danger">Wyloguj</button>
    </form>

    <!-- Nagłówek i opis systemu -->
    <header class="mb-5 text-center">
        <p class="lead text-muted">
            Zarządzaj swoimi projektami, spotkaniami, pytaniami i załącznikami w jednym intuicyjnym miejscu.
            Wybierz odpowiednią sekcję, aby przejść do szczegółowych funkcji.
        </p>
    </header>

    <!-- Dashboard: 3 sekcje -->
    <div class="row g-4">
        <!-- Sekcja Spotkania -->
        <div class="col-md-4">
            <div class="card card-hover shadow-custom">
                <div class="card-header meetings border-0">
                    <h5 class="mb-0">Harmonogram spotkań</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Przeglądaj nadchodzące spotkania powiązane z Twoim projektem.
                    </p>
                    @if($upcomingMeetings->count())
                        <ul class="list-group list-group-flush mb-3">
                            @foreach($upcomingMeetings as $meeting)
                                <li class="list-group-item">
                                    <a href="{{ route('projects.meetings.show', ['project' => $project->id, 'meeting' => $meeting->id]) }}"
                                       class="text-decoration-none">
                                        {{ \Illuminate\Support\Str::limit($meeting->title, 40) }}
                                    </a>
                                    <br>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($meeting->date)->format('d.m.Y H:i') }}
                                    </small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Brak nadchodzących spotkań.</p>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('projects.meetings', $project->id) }}" class="btn btn-custom" style="background-color: #F1C40F; color: #2d2d2d;">
                        Przejdź do spotkań
                    </a>
                </div>
            </div>
        </div>

        <!-- Sekcja Pytania i Odpowiedzi -->
        <div class="col-md-4">
            <div class="card card-hover shadow-custom">
                <div class="card-header questions border-0">
                    <h5 class="mb-0">Pytania i odpowiedzi</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Przeglądaj najnowsze pytania i odpowiedzi dotyczące projektu.
                    </p>
                    @if($recentQuestions->count())
                        <ul class="list-group list-group-flush mb-3">
                            @foreach($recentQuestions as $question)
                                <li class="list-group-item">
                                    <a href="{{ route('projects.questions.show', ['project' => $project->id, 'question' => $question->id]) }}"
                                       class="text-decoration-none">
                                        {{ \Illuminate\Support\Str::limit($question->title, 40) }}
                                    </a>
                                    <br>
                                    <small class="text-muted">
                                        Dodano: {{ $question->asked_at->format('d.m.Y H:i') }}
                                    </small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Brak pytań.</p>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('projects.questions', $project->id) }}" class="btn btn-custom" style="background-color: #FFC107; color: #1c1c1c;">
                        Przejdź do pytań
                    </a>
                </div>
            </div>
        </div>

        <!-- Sekcja Załączniki -->
        <div class="col-md-4">
            <div class="card card-hover shadow-custom">
                <div class="card-header attachments border-0">
                    <h5 class="mb-0">Załączniki</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Sprawdź wszystkie załączniki powiązane z projektem – dokumenty, obrazy i inne pliki.
                    </p>
                    @if($recentAttachments->count())
                        <ul class="list-group list-group-flush mb-3">
                            @foreach($recentAttachments as $attachment)
                                <li class="list-group-item">
                                    <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="text-decoration-none">
                                        {{ \Illuminate\Support\Str::limit($attachment->original_name, 40) }}
                                    </a>
                                    <br>
                                    <small class="text-muted">
                                        Dodano: {{ $attachment->created_at->format('d.m.Y H:i') }}
                                    </small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Brak załączników.</p>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('projects.attachments', $project->id) }}" class="btn btn-custom" style="background-color: #FFC107; color: #343a40;">
                        Przejdź do załączników
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Import czcionki Roboto */
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');
    body {
        font-family: 'Roboto', sans-serif;
    }
    /* Efekt hover dla kart */
    .card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    .shadow-custom {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    /* Style nagłówków kart */
    .card-header {
        padding: 1rem 1.25rem;
        font-weight: 700;
        font-size: 1.1rem;
    }
    .card-body {
        padding: 1.5rem;
        font-size: 0.95rem;
    }
    /* Kolorystyka dla poszczególnych sekcji */
    .card-header.meetings {
        background: linear-gradient(135deg, #1c1c1c, #3a3a3a);
        color: #F1C40F;
    }
    .card-header.questions {
        background: linear-gradient(135deg, #F39C12, #F1C40F);
        color: #1c1c1c;
    }
    .card-header.attachments {
        background: linear-gradient(135deg, #343a40, #495057);
        color: #FFC107;
    }
    /* Styl przycisków */
    .btn-custom {
        border-radius: 25px;
        font-weight: 500;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    .btn-custom:hover {
        opacity: 0.9;
    }
    /* Breadcrumb */
    .breadcrumb {
        font-size: 1rem;
    }
</style>
@endpush

@push('scripts')
    <script>
        var projectId = {{ $project->id }};
    </script>
    @vite('resources/js/dashboard/dashboard.js')
    @vite('resources/js/dashboard/meetings.js')
    @vite('resources/js/dashboard/questions.js')
    @vite('resources/js/dashboard/attachments.js')
@endpush
