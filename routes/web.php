<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\ProjectAttachmentCtrl;
use App\Http\Controllers\MeetingCommentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\QuestionController;
use App\Http\Controllers\API\AnswerController;

/*
|--------------------------------------------------------------------------
| Główna trasa – przekierowanie w zależności od stanu uwierzytelnienia
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (Auth::check()) {
        \Log::info('Użytkownik zalogowany, przekierowanie do projects.index');
        return redirect()->route('projects.index');
    } else {
        \Log::info('Użytkownik niezalogowany, przekierowanie do login');
        return redirect()->route('login');
    }
});

// Trasy publiczne związane z autoryzacją (logowanie, rejestracja, reset hasła)
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Trasy chronione autoryzacją
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::patch('/attachments/{attachment}/update-notatka', [\App\Http\Controllers\AttachmentController::class, 'updateNotatka'])
    ->name('attachments.updateNote');

    Route::patch('/projects/{project}/questions/{question}/mark-resolved', [\App\Http\Controllers\ProjectController::class, 'markResolved'])
    ->name('projects.questions.markResolved');

    Route::patch('/projects/{project}/questions/{question}/unmark-resolved', [\App\Http\Controllers\ProjectController::class, 'unmarkResolved'])
    ->name('projects.questions.unmarkResolved');


    // Załączniki projektu
    Route::post('/projects/{project}/attachments', [ProjectAttachmentCtrl::class, 'store'])
        ->name('project.attachments.store');

    // Komentarze do spotkań
    Route::post('/meetings/{meeting}/comments', [MeetingCommentController::class, 'store'])
        ->name('meeting.comments.store');

    // Operacje na pytaniach i odpowiedziach (usuwanie)
    Route::delete('/projects/{project}/questions/{question}', [ProjectController::class, 'destroyQuestion'])
        ->name('projects.questions.destroy');
    Route::delete('/projects/{project}/questions/{question}/answers/{answer}', [AnswerController::class, 'destroy'])
        ->name('projects.questions.answers.destroy');

    // Dashboard główny
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/');
    })->name('logout');

    Route::get('/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'show'])
    ->name('projects.show');


    // ======================
    // SPOTKANIA (Meetings)
    // ======================

    Route::get('/projects/{project}/meetings', [MeetingController::class, 'index'])
    ->name('projects.meetings');

    Route::get('/projects/{project}/meetings/create', [MeetingController::class, 'create'])
    ->name('meetings.create');

    Route::post('/projects/{project}/meetings', [MeetingController::class, 'store'])
    ->name('meetings.store');

    Route::get('/projects/{project}/meetings/{meeting}', [MeetingController::class, 'show'])
    ->name('projects.meetings.show');

    Route::delete('/projects/{project}/meetings/{meeting}', [MeetingController::class, 'destroy'])
    ->name('meetings.destroy');

    // Zmiana terminu spotkania
    Route::get('/projects/{project}/meetings/{meeting}/edit-date', [MeetingController::class, 'editDate'])
    ->name('meetings.editDate');

    Route::patch('/projects/{project}/meetings/{meeting}/update-date', [MeetingController::class, 'updateDate'])
    ->name('meetings.updateDate');

    Route::post('/meetings/{meeting}/comments', [MeetingCommentController::class, 'store'])
    ->name('meeting.comments.store');
    
    Route::delete('/projects/{project}/meetings/{meeting}/histories/{history}', [\App\Http\Controllers\MeetingController::class, 'destroyHistory'])
    ->name('meetings.histories.destroy');

    Route::delete('/meetings/comments/{comment}', [\App\Http\Controllers\MeetingCommentController::class, 'destroy'])
    ->name('meeting.comments.destroy');





    // ======================
    // PROJEKTY (Projects)
    // ======================

    // Lista projektów
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    // Tworzenie nowego projektu
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    // Szczegóły projektu
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

    // Sekcje projektu (pytania, załączniki)
    Route::get('/projects/{project}/questions', [ProjectController::class, 'questions'])->name('projects.questions');
    Route::get('/projects/{project}/attachments', [ProjectController::class, 'attachments'])->name('projects.attachments');

    // Pytania – dodawanie i przeglądanie
    Route::get('/projects/{project}/questions/create', [ProjectController::class, 'createQuestion'])->name('projects.questions.create');
    Route::post('/projects/{project}/questions', [ProjectController::class, 'storeQuestion'])->name('projects.questions.store');
    Route::get('/projects/{project}/questions/{question}', [ProjectController::class, 'showQuestion'])->name('projects.questions.show');

    // Odpowiedzi – dodawanie
    // Uwaga: Prawdopodobnie masz zdublowaną trasę do storeAnswer
    Route::post('/projects/{project}/questions/{question}/answers', [AnswerController::class, 'store'])
        ->name('projects.questions.answers.store');

    // Ewentualnie jeśli storeAnswer jest w ProjectController:
    // Route::post('/projects/{project}/questions/{question}/answers', [ProjectController::class, 'storeAnswer'])
    //     ->name('projects.questions.answers.store');

    // Generowanie PDF dla pytania
    Route::get('/projects/{project}/questions/{question}/pdf', [ProjectController::class, 'pdfQuestion'])
        ->name('projects.questions.pdf');

    // Profil użytkownika
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Trasy administracyjne (dla administratorów)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'can:access-admin-panel'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        // Zarządzanie użytkownikami:
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])
            ->name('admin.users.index');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])
            ->name('admin.users.edit');
        Route::patch('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])
            ->name('admin.users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])
            ->name('admin.users.destroy');

        // Zarządzanie projektami:
        Route::get('/projects', [\App\Http\Controllers\Admin\ProjectController::class, 'index'])
            ->name('admin.projects.index');
        Route::get('/projects/create', [\App\Http\Controllers\Admin\ProjectController::class, 'create'])
            ->name('admin.projects.create');
        Route::post('/projects', [\App\Http\Controllers\Admin\ProjectController::class, 'store'])
            ->name('admin.projects.store');
        Route::get('/projects/{project}/edit', [\App\Http\Controllers\Admin\ProjectController::class, 'edit'])
            ->name('admin.projects.edit');
        Route::patch('/projects/{project}', [\App\Http\Controllers\Admin\ProjectController::class, 'update'])
            ->name('admin.projects.update');
        Route::delete('/projects/{project}', [\App\Http\Controllers\Admin\ProjectController::class, 'destroy'])
            ->name('admin.projects.destroy');
    });


