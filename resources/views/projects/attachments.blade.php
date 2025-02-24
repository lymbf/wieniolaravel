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
            <li class="breadcrumb-item active" aria-current="page">Załączniki</li>
        </ol>
    </nav>

    <!-- Karta: Dodaj nowy załącznik (na górze) -->
    <div class="card modern-card mb-5 attachments-add-card">
        <div class="card-header attachments-add border-0">
            <h2 class="mb-0">
                <i class="fas fa-plus-circle"></i> Dodaj nowy załącznik
            </h2>
        </div>
        <div class="card-body">
            <form action="{{ route('project.attachments.store', $project->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="file" class="form-label">Wybierz plik:</label>
                    <input type="file" name="file" id="file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-light">
                    <i class="fas fa-cloud-upload-alt"></i> Dodaj załącznik
                </button>
            </form>
        </div>
    </div>

    <!-- Karta: Załączniki dodane ręcznie (czarne tło, biały tekst) -->
    <div class="card modern-card mb-5 attachments-manual-card">
        <div class="card-header attachments-manual border-0">
            <h2 class="mb-0">
                <i class="fas fa-upload"></i> Załączniki dodane ręcznie
            </h2>
        </div>
        <div class="card-body">
            @if($manualAttachments->count())
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="fas fa-file-alt"></i> Nazwa pliku</th>
                                <th><i class="far fa-clock"></i> Data dodania</th>
                                <th><i class="fas fa-user"></i> Dodany przez</th>
                                <th><i class="fas fa-sticky-note"></i> Notatka</th>
                                <th><i class="fas fa-cogs"></i> Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($manualAttachments as $attachment)
                                @php
                                    $fileUrl = asset('storage/' . $attachment->file_path);
                                    $notatka = $attachment->notatka ?? '';
                                    $addedBy = auth()->user()->name;
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ $fileUrl }}" target="_blank" class="text-decoration-none text-light">
                                            <i class="far fa-file"></i> {{ $attachment->original_name }}
                                        </a>
                                    </td>
                                    <td class="text-light">{{ \Carbon\Carbon::parse($attachment->created_at)->format('d.m.Y H:i') }}</td>
                                    <td class="text-light">{{ $addedBy }}</td>
                                    <td class="text-light">
                                        <form action="{{ route('attachments.updateNote', $attachment->id) }}" method="POST" class="d-flex align-items-center">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="notatka" value="{{ old('notatka', $notatka) }}" class="form-control form-control-sm note-input" placeholder="Dodaj notatkę">
                                            <button type="submit" class="btn btn-sm btn-light ms-1">Zapisz</button>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-light">
                                            <i class="fas fa-external-link-alt"></i> Otwórz
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">Brak ręcznie dodanych załączników.</p>
            @endif
        </div>
    </div>

    <!-- Karta: Załączniki z innych sekcji (biały wygląd, czarny tekst) -->
    <div class="card modern-card mb-5 attachments-other-card">
        <div class="card-header attachments-other border-0">
            <h2 class="mb-0">
                <i class="fas fa-link"></i> Załączniki z innych sekcji
            </h2>
        </div>
        <div class="card-body">
            @if($otherAttachments->count())
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th><i class="fas fa-file-alt"></i> Nazwa pliku</th>
                                <th><i class="far fa-clock"></i> Data dodania</th>
                                <th><i class="fas fa-user"></i> Dodany przez</th>
                                <th><i class="fas fa-sticky-note"></i> Notatka</th>
                                <th></th> {{-- Kolumna dla przycisku "znajdź" --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($otherAttachments as $attachment)
                                @php
                                    $fileUrl   = asset('storage/' . $attachment->file_path);
                                    $addedBy   = 'Nieznany';
                                    $sourceUrl = null;
                                    $sourceType = 'Nieokreślony';
                                    $notatka = $attachment->notatka ?? '';
                                    
                                    if ($attachment instanceof \App\Models\Attachment && $attachment->attachable) {
                                        if ($attachment->attachable_type === 'App\\Models\\Question') {
                                            $addedBy = $attachment->attachable->user->name ?? 'Nieznany';
                                            $sourceUrl = route('projects.questions.show', [
                                                'project'  => $project->id,
                                                'question' => $attachment->attachable->id
                                            ]);
                                            $sourceType = 'Pytania';
                                        } elseif ($attachment->attachable_type === 'App\\Models\\Answer') {
                                            $addedBy = $attachment->attachable->user->name ?? 'Nieznany';
                                            if(isset($attachment->attachable->question)) {
                                                $sourceUrl = route('projects.questions.show', [
                                                    'project'  => $project->id,
                                                    'question' => $attachment->attachable->question->id
                                                ]);
                                                $sourceType = 'Pytania';
                                            }
                                        }
                                    } elseif ($attachment instanceof \App\Models\MeetingCommentAttachment) {
                                        if ($attachment->meetingComment) {
                                            $addedBy = $attachment->meetingComment->user->name ?? 'Nieznany';
                                            if(isset($attachment->meetingComment->meeting) && $attachment->meetingComment->meeting) {
                                                $sourceUrl = route('projects.meetings.show', [
                                                    'project' => $project->id,
                                                    'meeting' => $attachment->meetingComment->meeting->id
                                                ]);
                                            } else {
                                                $sourceUrl = route('projects.meetings', ['project' => $project->id]);
                                            }
                                            $sourceType = 'Spotkania';
                                        } else {
                                            $addedBy = 'Brak informacji';
                                            $sourceUrl = route('projects.meetings', ['project' => $project->id]);
                                            $sourceType = 'Spotkania';
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ $fileUrl }}" target="_blank" class="text-decoration-none">
                                            <i class="far fa-file"></i> {{ $attachment->original_name ?? 'Załącznik' }}
                                        </a>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($attachment->created_at)->format('d.m.Y H:i') }}</td>
                                    <td>{{ $addedBy }}</td>
                                    <td>
                                        <form action="{{ route('attachments.updateNote', $attachment->id) }}" method="POST" class="d-flex align-items-center">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="notatka" value="{{ old('notatka', $notatka) }}" class="form-control form-control-sm note-input" placeholder="Notatka">
                                            <button type="submit" class="btn btn-sm btn-light ms-1">Zapisz</button>
                                        </form>
                                    </td>
                                    <td>
                                        @if($sourceUrl)
                                            <a href="{{ $sourceUrl }}" class="btn btn-sm btn-warning">
                                                znajdź
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">Brak załączników z innych sekcji.</p>
            @endif
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

    /* Nagłówek projektu */
    .project-title {
        font-size: 1.2rem;
        font-weight: bold;
        background-color: #007bff;
        color: #fff;
        padding: 6px 12px;
        border-radius: 5px;
    }

    /* Modern Card */
    .modern-card {
        border: none;
        border-radius: 10px;
        background-color: #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 1rem;
    }
    .modern-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }
    .modern-card .card-header {
        font-size: 1.2rem;
        font-weight: bold;
        padding: 1rem 1.25rem;
    }

    /* Karta: Dodaj nowy załącznik - Żółty */
    .attachments-add {
        background: #F1C40F;
        color: #1c1c1c;
    }

    /* Karta: Załączniki dodane ręcznie - Czarny */
    .attachments-manual {
        background: #000;
        color: #fff;
    }

    /* Karta: Załączniki z innych sekcji - Biały z czarnym tekstem */
    .attachments-other {
        background: #fff;
        color: #000;
    }

    /* Tabele */
    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }
    .table thead th {
        vertical-align: middle;
    }

    /* Przyciski custom */
    .btn-custom {
        border-radius: 25px;
        font-weight: 500;
        transition: background-color 0.3s ease, color 0.3s ease;
        background-color: #FFC107;
        color: #1c1c1c;
        border: none;
    }
    .btn-custom:hover {
        opacity: 0.9;
    }

    /* Breadcrumb */
    .breadcrumb {
        font-size: 1rem;
    }

    /* Styl nowoczesnego pola notatki */
    .note-input {
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 4px 8px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    .note-input:focus {
        border-color: #FFC107;
        box-shadow: 0 0 5px rgba(255, 193, 7, 0.5);
        outline: none;
    }
</style>
@endpush

@push('scripts')
<script>
    // Ewentualne skrypty
</script>
@endpush
