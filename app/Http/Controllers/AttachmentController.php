<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Project;
use App\Models\Attachment;


class AttachmentController extends Controller
{
    /**
     * Przesyłanie załącznika powiązanego z pytaniem lub odpowiedzią.
     *
     * Oczekuje, że w żądaniu znajdują się:
     * - 'file' – plik do przesłania
     * - 'attachmentable_type' – pełna nazwa klasy modelu (np. "App\Models\Question" lub "App\Models\Answer")
     * - 'attachmentable_id' – identyfikator rekordu
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'file'                => 'required|file|max:10240', // max 10MB
            'attachmentable_type' => 'required|string',
            'attachmentable_id'   => 'required|integer',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Zapis pliku w folderze 'attachments'
            $path = $file->store('attachments');

            // Utworzenie rekordu załącznika
            $attachment = Attachment::create([
                'attachable_id'   => $projectId,
                'attachable_type' => 'App\\Models\\Project', // żeby wskazać, że plik jest powiązany z projektem
                'file_path'       => $path,
                'original_name'   => $file->getClientOriginalName(),
            ]);
            

            return response()->json([
                'success'    => true,
                'attachment' => $attachment
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => 'Nie przesłano pliku.'
        ], 400);
    }

    /**
     * Usuwanie załącznika.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($attachmentId)
{
    $attachment = Attachment::findOrFail($attachmentId);
    
    // Usunięcie pliku
    Storage::delete($attachment->file_path);

    // Usunięcie wpisu w bazie
    $attachment->delete();

    return redirect()->back()->with('success', 'Załącznik został usunięty.');
}

public function updateNotatka(Request $request, Attachment $attachment)
    {
        $data = $request->validate([
            'notatka' => 'nullable|string'
        ]);

        $attachment->update(['notatka' => $data['notatka']]);

        return redirect()->back()->with('success', 'Notatka została zaktualizowana.');
    }

}
