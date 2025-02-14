<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Project; // Dodajemy model Project

class UserController extends Controller
{
    // Wyświetlenie listy użytkowników
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    // Formularz edycji użytkownika
    public function edit(User $user)
    {
        // Pobierz wszystkie projekty
        $allProjects = Project::all();
        // Pobierz tablicę ID projektów przypisanych do użytkownika
        $userProjects = $user->projects->pluck('id')->toArray();
        return view('admin.users.edit', compact('user', 'allProjects', 'userProjects'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'is_admin' => 'sometimes|boolean',
        ]);

        // Aktualizacja danych użytkownika
        $user->update($data);

        // Przypisanie projektów – zakładamy, że formularz przesyła tablicę 'projects'
        if($request->has('projects')) {
            $user->projects()->sync($request->input('projects'));
        } else {
            // Jeśli żaden projekt nie został zaznaczony, usuń wszystkie powiązania
            $user->projects()->detach();
        }

        return redirect()->route('admin.users.index')->with('status', 'Użytkownik zaktualizowany.');
    }

    // Usunięcie użytkownika
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('status', 'Użytkownik usunięty.');
    }
}
