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
            <li class="breadcrumb-item active" aria-current="page">Pytania i Odpowiedzi</li>
        </ol>
    </nav>

    <!-- Nagłówek strony -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="project-title">Pytania dla projektu: {{ $project->name }}</h1>
        <a href="{{ route('projects.questions.create', $project->id) }}" class="btn btn-custom shadow-lg">
            <i class="fas fa-plus"></i> Zadaj nowe pytanie
        </a>
    </div>

    @if($questions->isEmpty())
        <div class="alert alert-info text-center shadow-sm">
            Brak pytań w tym projekcie. Bądź pierwszy, który zada pytanie!
        </div>
    @else
        <!-- Lista pytań w jednokolumnowym układzie -->
        <div class="d-flex flex-column gap-4">
            @foreach($questions as $question)
                <div class="card modern-card {{ $question->status === 'resolved' ? 'resolved' : '' }}">
                    <div class="card-header questions">
                        <h5 class="mb-0">{{ $question->title }}</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-2">
                            <i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($question->asked_at)->format('d.m.Y H:i') }}
                            • <strong>{{ $question->user->name }}</strong>
                        </p>
                        <!-- Zwiększona widoczność treści pytania -->
                        <div class="question-text mb-3">
                            <strong>Treść:</strong> {{ $question->description }}
                        </div>

                        <!-- Załączniki pytania -->
                        @if($question->attachments->isNotEmpty())
                            <div class="attachments-section mt-2">
                                <strong>Załączniki:</strong>
                                <ul class="list-unstyled">
                                    @foreach($question->attachments as $attachment)
                                        <li>
                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="attachment-link">
                                                <i class="fas fa-paperclip"></i> {{ $attachment->original_name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <!-- Odpowiedzi pod pytaniem -->
                        @if($question->answers->isNotEmpty())
                            <div class="comments-section mt-3">
                                <h6 class="mb-2">Odpowiedzi ({{ $question->answers->count() }})</h6>
                                @foreach($question->answers as $answer)
                                    <div class="card comment-card mb-2 p-2">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span><i class="fas fa-user"></i> {{ $answer->user->name ?? 'Nieznany' }}</span>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($answer->answered_at)->format('d.m.Y H:i') }}</small>
                                        </div>
                                        <p class="comment-text mb-0">{{ $answer->answer_text }}</p>
                                        <!-- Załączniki odpowiedzi -->
                                        @if(isset($answer->attachments) && $answer->attachments->isNotEmpty())
                                            <div class="attachments-section mt-2">
                                                <strong>Załączniki odpowiedzi:</strong>
                                                <ul class="list-unstyled">
                                                    @foreach($answer->attachments as $attachment)
                                                        <li>
                                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="attachment-link">
                                                                <i class="fas fa-paperclip"></i> {{ $attachment->original_name }}
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
                    <div class="card-footer d-flex flex-column gap-2">
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('projects.questions.show', ['project' => $project->id, 'question' => $question->id]) }}" class="btn btn-custom">
                                Szczegóły pytania <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                            
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
                        
                        <!-- Formularz dodania odpowiedzi -->
                        <div class="mt-2">
                            <form action="{{ route('projects.questions.answers.store', ['project' => $project->id, 'question' => $question->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="input-group mb-2">
                                    <input type="text" name="answer_text" class="form-control" placeholder="Dodaj odpowiedź..." required>
                                </div>
                                <div class="mb-2">
                                    <input type="file" name="attachment" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Wyślij odpowiedź
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
    .question-text {
        font-size: 1rem;
        font-weight: 500;
        color: #333;
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
    .input-group .form-control {
        border-radius: 0.25rem 0 0 0.25rem;
    }
    .input-group .btn {
        border-radius: 0 0.25rem 0.25rem 0;
    }
    /* Styl dla rozwiązanych pytań */
    .resolved {
        border: 2px solid #28a745;
        background-color: #e6ffed;
    }
</style>
@endpush

@push('scripts')
<script>
    var projectId = {{ $project->id }};
</script>
@endpush
