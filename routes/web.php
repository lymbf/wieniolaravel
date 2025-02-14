<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Główna trasa – przekierowanie w zależności od stanu uwierzytelnienia
Route::get('/', function () {
    return Auth::check() 
        ? redirect()->route('projects.index')
        : redirect()->route('login');
});

// Trasy chronione autoryzacją
Route::middleware(['auth'])->group(function () {
    // Dashboard – logika przekierowania może być umieszczona w DashboardController
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Trasy dla projektów użytkownika
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    
    // Trasy dla profilu
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Trasy administracyjne – dostępne tylko dla administratorów
Route::middleware(['auth', 'can:access-admin-panel'])->prefix('admin')->group(function () {
    // Admin Dashboard
    Route::get('/', function () {
         return view('admin.dashboard');
    })->name('admin.dashboard');
    
    // Zarządzanie użytkownikami
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('admin.users.edit');
    Route::patch('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
    
    // Zarządzanie projektami
    Route::get('/projects', [\App\Http\Controllers\Admin\ProjectController::class, 'index'])->name('admin.projects.index');
    Route::get('/projects/create', [\App\Http\Controllers\Admin\ProjectController::class, 'create'])->name('admin.projects.create');
    Route::post('/projects', [\App\Http\Controllers\Admin\ProjectController::class, 'store'])->name('admin.projects.store');
    Route::get('/projects/{project}/edit', [\App\Http\Controllers\Admin\ProjectController::class, 'edit'])->name('admin.projects.edit');
    Route::patch('/projects/{project}', [\App\Http\Controllers\Admin\ProjectController::class, 'update'])->name('admin.projects.update');
    Route::delete('/projects/{project}', [\App\Http\Controllers\Admin\ProjectController::class, 'destroy'])->name('admin.projects.destroy');
});

// Załaduj trasy autoryzacyjne (np. logowanie, rejestracja, reset hasła)
require __DIR__.'/auth.php';
