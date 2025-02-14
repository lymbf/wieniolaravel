<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $projects = $user->projects; // Upewnij się, że relacja projects została dodana do modelu User

        // Jeśli użytkownik ma tylko jeden projekt, przekieruj go bezpośrednio do dashboardu projektu
        if ($projects->count() === 1) {
            return redirect()->route('projects.show', $projects->first()->id);
        }

        // W przeciwnym wypadku wyświetl listę projektów
        return view('projects.index', compact('projects'));
    }

    public function show(Project $project)
    {
        // Tutaj możesz sprawdzić, czy zalogowany użytkownik ma dostęp do danego projektu
        // i wyświetlić szczegóły projektu oraz dashboard z modułami (pytania, spotkania, załączniki)
        return view('projects.show', compact('project'));
    }

    // Pozostałe metody resource możesz dodać w miarę potrzeby
}
