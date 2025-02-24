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
                <a href="{{ route('projects.questions', $project->id) }}" class="text-decoration-none">
                    Pytania i Odpowiedzi
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $question->title }}</li>
        </ol>
    </nav>

    <!-- Nagłówek strony z przyciskiem PDF/Drukuj -->
    <header class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="project-title">Szczegóły pytania</h2>
        <div>
            <a href="{{ route('projects.questions.pdf', ['project' => $project->id, 'question' => $question->id]) }}" 
               class="btn btn-outline-secondary btn-sm me-2" target="_blank" title="Wydrukuj lub wygeneruj PDF">
                <i class="fas fa-print"></i> PDF
            </a>
            <a href="{{ route('projects.questions', $project->id) }}" class="btn btn-lg btn-outline-primary">
                ⮜ Powrót do pytań
            </a>
        </div>
    </header>

    <!-- Karta z pytaniem -->
    <div class="card modern-card question-details-card mb-5">
        <div class="card-header details-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0 text-light">{{ $question->title }}</h4>
                @if(auth()->user()->isAdmin())
                    <form action="{{ route('projects.questions.destroy', ['project' => $project->id, 'question' => $question->id]) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć to pytanie?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
        <div class="card-body">
            <!-- Górny wiersz: data i autor -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted small">
                    Zadane: {{ $question->asked_at->format('Y-m-d H:i') }} przez <strong>{{ $question->user->name }}</strong>
                </div>
            </div>

            <!-- Opis pytania -->
            <div class="mb-3">
                <strong>Treść pytania:</strong>
                <p class="question-description">{{ $question->description }}</p>
            </div>

            <!-- Informacje o statusie -->
            <div class="mb-3">
                <strong>Status:</strong>
                @if($question->status === 'in_progress')
                    <span class="badge bg-secondary">W trakcie</span>
                @else
                    <span class="badge bg-success">Rozwiązane</span>
                @endif
                @if($question->status_changed_at)
                    <small class="text-muted ms-2">
                        (zmienione przez {{ $question->status_changed_by }}: {{ $question->status_changed_at->format('Y-m-d H:i') }})
                    </small>
                @endif
            </div>

            <!-- Załączniki do pytania -->
            @if($question->attachments->isNotEmpty())
                <div class="attachments-section mb-3">
                    <strong>Załączniki:</strong>
                    <ul class="list-unstyled mb-0">
                        @foreach($question->attachments as $attachment)
                            <li>
                                <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="attachment-link">
                                    <i class="fas fa-paperclip"></i> {{ $attachment->original_name }}
                                </a>
                                @if(auth()->user()->isAdmin())
                                    <form action="{{ route('attachments.destroy', $attachment->id) }}" method="POST" class="d-inline ms-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć ten załącznik?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Przycisk do zmiany statusu -->
            <div class="d-flex gap-2 flex-wrap mt-2">
                @if($question->status === 'in_progress')
                    <form action="{{ route('projects.questions.markResolved', ['project' => $project->id, 'question' => $question->id]) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-outline-success">
                            Oznacz jako rozwiązane
                        </button>
                    </form>
                @else
                    <form action="{{ route('projects.questions.unmarkResolved', ['project' => $project->id, 'question' => $question->id]) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-outline-warning">
                            Cofnij rozwiązanie
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Sekcja odpowiedzi -->
    <div class="card modern-card mb-5">
        <div class="card-header details-header">
            <h5 class="mb-0 text-light">Odpowiedzi</h5>
        </div>
        <div class="card-body answers-section">
            @if($question->answers->isEmpty())
                <p class="text-muted">Brak odpowiedzi. Bądź pierwszy, który odpowie!</p>
            @else
                <div class="d-flex flex-column gap-3">
                    @foreach($question->answers as $answer)
                        <div class="card answer-card p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="text-muted small">
                                    Odpowiedź: {{ $answer->answered_at->format('Y-m-d H:i') }} przez <strong>{{ $answer->user->name }}</strong>
                                </div>
                                @if(auth()->user()->isAdmin())
                                    <form action="{{ route('projects.questions.answers.destroy', ['project' => $project->id, 'question' => $question->id, 'answer' => $answer->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć tę odpowiedź?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <p class="answer-text mb-3">{{ $answer->answer_text }}</p>
                            @if($answer->attachments->isNotEmpty())
                                <div class="attachments-section">
                                    <strong>Załączniki:</strong>
                                    <ul class="list-unstyled mb-0">
                                        @foreach($answer->attachments as $attachment)
                                            <li>
                                                <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="attachment-link">
                                                    <i class="fas fa-paperclip"></i> {{ $attachment->original_name }}
                                                </a>
                                                @if(auth()->user()->isAdmin())
                                                    <form action="{{ route('attachments.destroy', $attachment->id) }}" method="POST" class="d-inline ms-2">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć ten załącznik?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
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
    </div>

    <!-- Formularz dodania odpowiedzi -->
    <div class="card modern-card mb-5 p-4">
        <h5 class="section-title mb-3">Dodaj odpowiedź</h5>
        <form action="{{ route('projects.questions.answers.store', ['project' => $project->id, 'question' => $question->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="answer_text" class="form-label">Twoja odpowiedź:</label>
                <textarea name="answer_text" id="answer_text" rows="3" class="form-control" placeholder="Wpisz odpowiedź" required></textarea>
            </div>
            <div class="mb-3">
                <label for="attachment" class="form-label">Załącznik (opcjonalnie):</label>
                <input type="file" name="attachment" id="attachment" class="form-control">
            </div>
            <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-paper-plane"></i> Wyślij odpowiedź
            </button>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

    body {
        font-family: 'Roboto', sans-serif;
    }

    /* Breadcrumb */
    .breadcrumb {
        font-size: 1rem;
    }

    /* Nagłówek projektu w breadcrumb */
    .project-title {
        font-size: 1.4rem;
        font-weight: bold;
        background-color: #F1C40F;
        color: #000;
        padding: 6px 12px;
        border-radius: 5px;
    }

    /* Karta w stylu "modern-card" */
    .modern-card {
        border: none;
        border-radius: 10px;
        background-color: #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 1rem;
    }
    .modern-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.15);
    }

    /* Nagłówek karty szczegółów pytania */
    .question-details-card .card-header.details-header {
        background: linear-gradient(135deg, #444, #666);
        color: #F1C40F;
    }

    /* Nagłówek karty w sekcji odpowiedzi */
    .card-header.details-header {
        background: linear-gradient(135deg, #444, #666);
        color: #F1C40F;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    /* Tytuł sekcji */
    .section-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-top: 0;
    }

    /* Opis pytania */
    .question-description {
        font-size: 1rem;
        color: #333;
    }

    /* Karta odpowiedzi */
    .answer-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #fafafa;
        transition: background-color 0.3s ease;
    }
    .answer-card:hover {
        background-color: #f3f3f3;
    }

    .answer-text {
        font-size: 1rem;
        color: #444;
    }

    /* Załączniki */
    .attachments-section {
        margin-top: 10px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
    }
    .attachment-link {
        text-decoration: none;
        color: #007bff;
    }
    .attachment-link:hover {
        text-decoration: underline;
    }

    /* Formularz dodawania odpowiedzi */
    .answer-form-section .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        background-color: #fff;
    }
</style>
@endpush

@push('scripts')
<script>
    var projectId = {{ $project->id }};
</script>
@endpush
