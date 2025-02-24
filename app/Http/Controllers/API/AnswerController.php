<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Log;

class AnswerController extends Controller
{
    /**
     * Dodaje nową odpowiedź do pytania i przekierowuje do szczegółowego widoku pytania.
     */
    public function store(Request $request, \App\Models\Project $project, \App\Models\Question $question)
{
    \Log::info('Dane przesłane do AnswerController:', [
        'request' => $request->all(),
        'has_file' => $request->hasFile('attachment')
    ]);

    $validatedData = $request->validate([
        'answer_text' => 'required|string',
        'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20480'
    ]);

    $user = $request->user();

    $data = [
        'question_id' => $question->id,
        'user_id'     => $user->id,
        'answer_text' => $validatedData['answer_text'],
        'answered_at' => Carbon::now()
    ];

    $answer = Answer::create($data);

    // Obsługa opcjonalnego załącznika
    if ($request->hasFile('attachment')) {
        $file = $request->file('attachment');
        $originalName = $file->getClientOriginalName();
        $fileName = $this->uniqueFileName($originalName);

        if (in_array($file->getClientMimeType(), ['image/jpeg', 'image/png'])) {
            $path = 'attachments/' . $fileName;

            $image = \Intervention\Image\Facades\Image::make($file)
                ->resize(1920, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode(null, 80);

            \Illuminate\Support\Facades\Storage::put($path, (string) $image);
        } else {
            $path = $file->storeAs('attachments', $fileName);
        }

        $answer->attachments()->create([
            'attachable_id'   => $answer->id,
            'attachable_type' => get_class($answer),
            'file_path'       => $path,
            'original_name'   => $originalName,
        ]);
    }

    return redirect()->route('projects.questions', [
        'project' => $project->id,
        'question' => $question->id
    ])->with('success', 'Odpowiedź została dodana.');
}


    public function destroy($projectId, $questionId, $answerId)
{
    $answer = Answer::findOrFail($answerId);

    // 🛑 **Dodajemy sprawdzenie uprawnień**
    if (!auth()->user()->isAdmin()) {
        return redirect()->route('projects.questions.show', ['project' => $projectId, 'question' => $questionId])
            ->with('error', 'Nie masz uprawnień do usunięcia tej odpowiedzi.');
    }

    // Usunięcie załączników odpowiedzi
    foreach ($answer->attachments as $attachment) {
        Storage::delete($attachment->file_path);
        $attachment->delete();
    }

    // Usunięcie odpowiedzi
    $answer->delete();

    return redirect()->route('projects.questions.show', ['project' => $projectId, 'question' => $questionId])
        ->with('success', 'Odpowiedź została usunięta.');
}



    /**
     * Generuje unikalną nazwę pliku, aby uniknąć nadpisania istniejących plików.
     */
    protected function uniqueFileName($originalName)
    {
        $name = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $fileName = $name . '.' . $extension;
        $counter = 1;

        while (Storage::exists('attachments/' . $fileName)) {
            $fileName = $name . '_' . $counter . '.' . $extension;
            $counter++;
        }

        return $fileName;
    }
}
