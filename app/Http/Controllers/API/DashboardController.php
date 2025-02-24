<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $projects = auth()->user()->projects;

        if ($projects->count() === 1) {
            return redirect()->route('projects.show', $projects->first()->id);
        } elseif ($projects->count() > 1) {
            return redirect()->route('projects.index');
        }

        // Jeśli brak projektów, przekieruj do widoku informacyjnego o braku projektów
        return view('noproject');
    }
}
