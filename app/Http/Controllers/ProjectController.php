<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Question;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Attachment;
use App\Models\Meeting;
use Dompdf\Dompdf;
use setasign\Fpdi\Fpdi;

class ProjectController extends Controller
{
    /**
     * Wyświetla listę projektów użytkownika.
     */
    public function index()
    {
        $user = auth()->user();
        $projects = $user->projects;

        // Jeśli użytkownik ma tylko jeden projekt, przekieruj go do jego widoku
        if ($projects->count() === 1) {
            return redirect()->route('projects.show', $projects->first()->id);
        }

        return view('projects.index', compact('projects'));
    }

    /**
     * Usuwa pytanie wraz z załącznikami i odpowiedziami.
     */
    public function destroyQuestion(Project $project, Question $question)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('projects.questions', $project->id)
                ->with('error', 'Nie masz uprawnień do usunięcia tego pytania.');
        }

        // Usuwanie wszystkich załączników pytania
        $question->attachments()->delete();
        // Usuwanie odpowiedzi (wraz z ich załącznikami – zależnie od relacji)
        $question->answers()->delete();
        // Usuwanie samego pytania
        $question->delete();

        return redirect()->route('projects.questions', $project->id)
            ->with('success', 'Pytanie zostało usunięte.');
    }

    /**
     * Formularz tworzenia nowego projektu.
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Dashboard projektu.
     */
    public function show(Project $project)
    {
        // Najnowsze pytania (limit 5)
        $recentQuestions = Question::where('project_id', $project->id)
            ->orderBy('asked_at', 'desc')
            ->limit(5)
            ->get();

        // Najbliższe spotkania (limit 5)
        $upcomingMeetings = Meeting::where('project_id', $project->id)
            ->where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->limit(5)
            ->get();

        // Ostatnie załączniki (limit 5)
        $recentAttachments = Attachment::where('attachable_type', 'App\\Models\\Project')
            ->where('attachable_id', $project->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('projects.show', [
            'project'           => $project,
            'recentQuestions'   => $recentQuestions,
            'upcomingMeetings'  => $upcomingMeetings,
            'recentAttachments' => $recentAttachments,
        ]);
    }

    /**
     * Lista pytań w projekcie.
     */
    public function questions(Project $project)
    {
        $questions = Question::with(['user', 'answers.user', 'answers.attachments', 'attachments'])
            ->where('project_id', $project->id)
            ->orderBy('asked_at', 'desc')
            ->get();

        return view('projects.questions', compact('project', 'questions'));
    }

    /**
     * Formularz zadawania nowego pytania.
     */
    public function createQuestion(Project $project)
    {
        return view('questions.create', compact('project'));
    }

    /**
     * Zapis nowego pytania (z opcjonalnym załącznikiem).
     */
    public function storeQuestion(Request $request, Project $project)
    {
        $validatedData = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'attachment'  => 'nullable|file|max:10240'
        ]);

        $user = $request->user();

        $question = \App\Models\Question::create([
            'project_id'  => $project->id,
            'user_id'     => $user->id,
            'title'       => $validatedData['title'],
            'description' => $validatedData['description'],
            'asked_at'    => Carbon::now(),
            'status'      => 'in_progress'
        ]);

        // Jeśli przesłano załącznik, zapisz i powiąż z pytaniem
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalName = $file->getClientOriginalName();
            $fileName = $this->uniqueFileName($originalName);
            // Zapis w dysku 'public'
            $path = $file->storeAs('attachments', $fileName, 'public');

            $question->attachments()->create([
                'file_path'     => $path,
                'original_name' => $originalName,
            ]);
        }

        return redirect()->route('projects.questions', $project->id)
            ->with('success', 'Pytanie zostało zadane.');
    }

    public function markResolved(\App\Models\Project $project, \App\Models\Question $question)
    {
        $question->update([
            'status' => 'resolved',
            'status_changed_by' => auth()->user()->name, // zapisanie nazwy użytkownika
            'status_changed_at' => now(),
        ]);
    
        return redirect()->back()->with('success', 'Pytanie zostało oznaczone jako rozwiązane.');
    }
    
    public function unmarkResolved(\App\Models\Project $project, \App\Models\Question $question)
    {
        $question->update([
            'status' => 'in_progress',
            'status_changed_by' => auth()->user()->name, // zapisanie kto dokonał zmiany
            'status_changed_at' => now(),
        ]);
    
        return redirect()->back()->with('success', 'Oznaczenie pytania jako rozwiązane zostało cofnięte.');
    }
    


    /**
     * Szczegóły pytania (widok).
     */
    public function showQuestion(Project $project, Question $question)
    {
        $question->load(['user', 'answers.user', 'answers.attachments', 'attachments']);
        return view('projects.question_details', compact('project', 'question'));
    }

    /**
     * Dodaje odpowiedź do pytania (z opcjonalnym załącznikiem).
     */
    public function storeAnswer(Request $request, Project $project, Question $question)
    {
        $validated = $request->validate([
            'answer_text' => 'required|string',
            'attachment'  => 'nullable|file|max:10240',
        ]);

        $answer = $question->answers()->create([
            'user_id'     => auth()->id(),
            'answer_text' => $validated['answer_text'],
            'answered_at' => now(),
        ]);

        // Jeśli przesłano załącznik, zapisz i powiąż z odpowiedzią
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalName = $file->getClientOriginalName();
            $fileName = $this->uniqueFileName($originalName);
            $path = $file->storeAs('attachments', $fileName, 'public');

            $answer->attachments()->create([
                'file_path'     => $path,
                'original_name' => $originalName,
            ]);
        }

        return redirect()->route('projects.questions.show', [
            'project'  => $project->id,
            'question' => $question->id
        ])->with('success', 'Odpowiedź została dodana.');
    }

    /**
     * Wyświetla listę spotkań.
     */
    public function meetings(Project $project)
    {
        $meetings = Meeting::where('project_id', $project->id)
            ->orderBy('date', 'asc')
            ->get();

        return view('projects.meetings', compact('project', 'meetings'));
    }

    /**
     * Załączniki projektu (różne sekcje).
     */
    public function attachments(Project $project)
    {
        // 1. Załączniki dodane ręcznie
        $manualAttachments = Attachment::where('attachable_type', 'App\\Models\\Project')
            ->where('attachable_id', $project->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Załączniki z pytań i odpowiedzi
        $questionAnswerAttachments = Attachment::where(function($query) use ($project) {
            // Pytania
            $query->where(function($q) use ($project) {
                $q->where('attachable_type', 'App\\Models\\Question')
                  ->whereIn('attachable_id', function($subquery) use ($project) {
                      $subquery->select('id')
                          ->from('questions')
                          ->where('project_id', $project->id);
                  });
            })
            // Odpowiedzi
            ->orWhere(function($q) use ($project) {
                $q->where('attachable_type', 'App\\Models\\Answer')
                  ->whereIn('attachable_id', function($subquery) use ($project) {
                      $subquery->select('id')
                          ->from('answers')
                          ->whereIn('question_id', function($subquery2) use ($project) {
                              $subquery2->select('id')
                                  ->from('questions')
                                  ->where('project_id', $project->id);
                          });
                  });
            });
        })->orderBy('created_at', 'desc')->get();

        // 3. Załączniki z komentarzy spotkań
        $meetingCommentAttachments = \App\Models\MeetingCommentAttachment::whereIn('meeting_comment_id', function($subquery) use ($project) {
            $subquery->select('id')
                ->from('meeting_comments')
                ->whereIn('meeting_id', function($subquery2) use ($project) {
                    $subquery2->select('id')
                        ->from('meetings')
                        ->where('project_id', $project->id);
                });
        })->orderBy('created_at', 'desc')->get();

        // 4. Połącz i posortuj
        $otherAttachments = $questionAnswerAttachments
            ->merge($meetingCommentAttachments)
            ->sortByDesc('created_at');

        return view('projects.attachments', [
            'project'           => $project,
            'otherAttachments'  => $otherAttachments,
            'manualAttachments' => $manualAttachments,
        ]);
    }

    /**
     * Generuje unikalną nazwę pliku, aby uniknąć nadpisania.
     */
    protected function uniqueFileName($originalName)
    {
        $name = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $fileName = $originalName;
        $counter = 1;

        // Sprawdza, czy plik już istnieje w attachments
        while (Storage::disk('public')->exists('attachments/' . $fileName)) {
            $fileName = $name . '_' . $counter . '.' . $extension;
            $counter++;
        }

        return $fileName;
    }

    /**
     * Generowanie PDF i scalanie z załącznikami PDF.
     */
    public function pdfQuestion(Project $project, Question $question)
{
    // Załaduj potrzebne relacje
    $question->load(['user', 'answers.user', 'answers.attachments', 'attachments']);

    // 1. Wygeneruj główny PDF przy użyciu Dompdf
    $dompdf = new Dompdf([
        'enable_remote' => true,
    ]);
    $dompdf->set_option('isRemoteEnabled', true);
    $html = view('projects.question_pdf', [
        'project'  => $project,
        'question' => $question
    ])->render();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $mainPdfContent = $dompdf->output();

    // 2. Zapisz główny PDF do pliku tymczasowego
    $tempFile = tempnam(sys_get_temp_dir(), 'pdf');
    file_put_contents($tempFile, $mainPdfContent);

    // 3. Utwórz obiekt FPDI i załaduj główny PDF z pliku tymczasowego
    $pdfMerger = new \setasign\Fpdi\Fpdi();
    $pageCount = $pdfMerger->setSourceFile($tempFile);
    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        $tplId = $pdfMerger->importPage($pageNo);
        $size = $pdfMerger->getTemplateSize($tplId);
        $pdfMerger->AddPage('P', [$size['width'], $size['height']]);
        $pdfMerger->useTemplate($tplId);
    }

    // 4. Dołącz załączniki PDF z pytania
    foreach ($question->attachments as $attachment) {
        $ext = strtolower(pathinfo($attachment->original_name, PATHINFO_EXTENSION));
        if ($ext === 'pdf') {
            $filePath = public_path('storage/' . $attachment->file_path);
            if (file_exists($filePath)) {
                $attachPageCount = $pdfMerger->setSourceFile($filePath);
                for ($p = 1; $p <= $attachPageCount; $p++) {
                    $tplId = $pdfMerger->importPage($p);
                    $size = $pdfMerger->getTemplateSize($tplId);
                    $pdfMerger->AddPage('P', [$size['width'], $size['height']]);
                    $pdfMerger->useTemplate($tplId);
                }
            }
        }
    }

    // 5. Dołącz załączniki PDF z odpowiedzi
    foreach ($question->answers as $answer) {
        foreach ($answer->attachments as $attachment) {
            $ext = strtolower(pathinfo($attachment->original_name, PATHINFO_EXTENSION));
            if ($ext === 'pdf') {
                $filePath = public_path('storage/' . $attachment->file_path);
                if (file_exists($filePath)) {
                    $attachPageCount = $pdfMerger->setSourceFile($filePath);
                    for ($p = 1; $p <= $attachPageCount; $p++) {
                        $tplId = $pdfMerger->importPage($p);
                        $size = $pdfMerger->getTemplateSize($tplId);
                        $pdfMerger->AddPage('P', [$size['width'], $size['height']]);
                        $pdfMerger->useTemplate($tplId);
                    }
                }
            }
        }
    }

    // 6. Usuń plik tymczasowy
    unlink($tempFile);

    // 7. Zwróć scalony PDF
    return response($pdfMerger->Output('S'), 200)
           ->header('Content-Type', 'application/pdf');
}

    
}
