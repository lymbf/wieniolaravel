<?php

namespace App\Http\Controllers\API;

use App\Models\Question;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class QuestionController extends Controller {
    /**
     * Pobiera listę pytań dla danego projektu.
     */
    public function index(Request $request)
    {
        $questions = Question::with(['user', 'answers.user', 'answers.attachments', 'attachments'])
            ->where('project_id', $request->query('project_id'))
            ->orderBy('asked_at', 'desc')
            ->get();

        return response()->json($questions);
    }

    /**
     * Tworzy nowe pytanie, ustawiając datę zadania oraz obsługując opcjonalny załącznik.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'project_id'  => 'required|exists:projects,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'attachment'  => 'nullable|file'
        ]);

        $user = $request->user();

        $data = [
            'project_id'  => $validatedData['project_id'],
            'user_id'     => $user->id,
            'title'       => $validatedData['title'],
            'description' => $validatedData['description'],
            'asked_at'    => Carbon::now(),
            'status'      => 'in_progress'
        ];

        $question = Question::create($data);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalName = $file->getClientOriginalName();
            $fileName = $this->uniqueFileName($originalName);
            $path = $file->storeAs('attachments', $fileName);
            $question->attachments()->create([
                'file_path'     => $path,
                'original_name' => $originalName,
            ]);
        }

        // Ładujemy relacje, aby odpowiedź zawierała dane użytkownika i załączników
        $question->load('user', 'attachments');

        return response()->json($question, 201);
    }
    
    /**
     * Wyświetla szczegóły pytania wraz z relacjami.
     */
    public function show(Question $question)
    {
        $question->load(['user', 'answers.user', 'answers.attachments', 'attachments']);
        return response()->json($question);
    }

    /**
     * Aktualizuje dane pytania.
     */
    public function update(Request $request, Question $question)
    {
        $data = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'status'      => 'in:in_progress,resolved',
        ]);
        $question->update($data);
        return response()->json($question);
    }

    /**
     * Usuwa pytanie.
     */
    public function destroy($projectId, $questionId)
{
    $question = Question::findOrFail($questionId);
    
    // Usunięcie załączników
    foreach ($question->attachments as $attachment) {
        Storage::delete($attachment->file_path);
        $attachment->delete();
    }

    // Usunięcie odpowiedzi na to pytanie
    foreach ($question->answers as $answer) {
        foreach ($answer->attachments as $attachment) {
            Storage::delete($attachment->file_path);
            $attachment->delete();
        }
        $answer->delete();
    }

    // Usunięcie pytania
    $question->delete();

    return redirect()->route('projects.questions', ['project' => $projectId])
        ->with('success', 'Pytanie zostało usunięte.');
}

    
    
    /**
     * Generuje unikalną nazwę pliku, aby uniknąć nadpisania istniejących plików.
     */
    protected function uniqueFileName($originalName)
    {
        $name = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $fileName = $originalName;
        $counter = 1;
        while (Storage::exists('attachments/' . $fileName)) {
            $fileName = $name . '_' . $counter . '.' . $extension;
            $counter++;
        }
        return $fileName;
    }
}
