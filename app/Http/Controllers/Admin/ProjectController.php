<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Wyświetla listę wszystkich projektów.
     */
    public function index()
    {
        $projects = Project::all();
        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Wyświetla formularz tworzenia nowego projektu.
     */
    public function create()
    {
        return view('admin.projects.create');
    }

    /**
     * Zapisuje nowy projekt w bazie danych.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Project::create($data);

        return redirect()->route('admin.projects.index')->with('status', 'Projekt utworzony.');
    }

    /**
     * Wyświetla formularz edycji istniejącego projektu.
     */
    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    /**
     * Aktualizuje dane projektu w bazie.
     */
    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update($data);

        return redirect()->route('admin.projects.index')->with('status', 'Projekt zaktualizowany.');
    }

    /**
     * Usuwa projekt z bazy danych.
     */
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('admin.projects.index')->with('status', 'Projekt usunięty.');
    }
}
