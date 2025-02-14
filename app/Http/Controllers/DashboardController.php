<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $projects = auth()->user()->projects;

        // Jeśli użytkownik ma tylko jeden projekt, przekieruj go bezpośrednio do dashboardu tego projektu
        if ($projects->count() === 1) {
            return redirect()->route('projects.show', $projects->first()->id);
        }
        
        // Jeśli użytkownik ma więcej niż jeden projekt, przekieruj na stronę wyboru projektów
        if ($projects->count() > 1) {
            return redirect()->route('projects.index');
        }

        // Jeśli nie ma żadnych projektów, możesz przekierować do strony informacyjnej lub pozostawić domyślny dashboard
        return view('dashboard');
    }
}
