<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Project;
use App\Models\Attachment;

class ProjectAttachmentCtrl extends Controller
{
    /**
     * Przesyłanie nowego załącznika dla projektu.
     * Metoda przyjmuje obiekt $project z Route Model Binding:
     * Route::post('/projects/{project}/attachments', [ProjectAttachmentCtrl::class, 'store'])
     *      ->name('project.attachments.store');
     */
    public function store(Request $request, Project $project)
    {
        // Debug: sprawdź, czy mamy poprawny obiekt projektu
        // (Możesz to potem usunąć)
        Log::info('Debug Project:', ['id' => $project->id, 'object' => $project]);
        // dd($project->id, $project); // Odkomentuj w razie potrzeby

        // Jeśli nie ma projektu lub ID jest puste, zwróć błąd
        if (!$project || !$project->id) {
            return redirect()->back()->with('error', 'Nie znaleziono projektu o danym ID.');
        }

        // Walidacja pliku (maksymalnie 10MB)
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        // Sprawdź, czy przesłano plik
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Zapis pliku w folderze 'attachments' na dysku publicznym
            $path = $file->store('attachments', 'public');

            // Logowanie dla pewności
            Log::info('Plik został zapisany na dysku publicznym:', ['path' => $path]);

            // Utworzenie rekordu załącznika w bazie
            $attachment = Attachment::create([
                'attachable_id'   => $project->id,
                'attachable_type' => 'App\\Models\\Project',
                'file_path'       => $path,
                'original_name'   => $file->getClientOriginalName(),
            ]);

            Log::info('Załącznik został utworzony w bazie:', ['attachment' => $attachment]);

            return redirect()->back()->with('success', 'Załącznik został dodany.');
        }

        // Jeśli nie przesłano pliku
        return redirect()->back()->with('error', 'Nie udało się przesłać pliku.');
    }

    /**
     * Usuwanie załącznika.
     */
    public function destroy($id)
    {
        $attachment = Attachment::findOrFail($id);

        // Usunięcie pliku z dysku
        Storage::delete($attachment->file_path);
        // Usunięcie rekordu z bazy
        $attachment->delete();

        return redirect()->back()->with('success', 'Załącznik został usunięty.');
    }
}
