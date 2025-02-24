<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Providers\RouteServiceProvider;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
{
    $request->authenticate();
    $request->session()->regenerate();

    // Pobranie zalogowanego użytkownika
    $user = auth()->user();

    // Sprawdzenie liczby projektów użytkownika
    if ($user->projects->count() > 1) {
        // Jeśli użytkownik ma więcej niż jeden projekt – przekieruj do listy projektów
        return redirect()->route('projects.index');
    } elseif ($user->projects->count() === 1) {
        // Jeśli użytkownik ma tylko jeden projekt – przekieruj do dashboardu tego projektu
        return redirect()->route('projects.show', $user->projects->first()->id);
    } else {
        // Jeśli brak projektów, możesz przekierować do innego widoku (np. informującego o braku projektów)
        return redirect()->route('dashboard.noproject');
    }
}


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
