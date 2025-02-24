<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    /**
     * Pobieranie listy załączników.
     */
    public function index()
    {
        // Pobierz załączniki z bazy danych, np.:
        // $attachments = Attachment::all();
        return response()->json(['attachments' => []]);
    }

    /**
     * Dodawanie nowego załącznika.
     */
    public function store(Request $request)
    {
        // Walidacja pliku
        $data = $request->validate([
            'file' => 'required|file|max:10240' // max 10MB
        ]);

        // Przetwarzanie przesłanego pliku:
        // $path = $request->file('file')->store('attachments');
        // Możesz również zapisać informacje o pliku w bazie danych:
        // Attachment::create(['file_path' => $path, 'original_name' => $request->file('file')->getClientOriginalName()]);

        return response()->json(['message' => 'Załącznik został dodany.'], 201);
    }
}
