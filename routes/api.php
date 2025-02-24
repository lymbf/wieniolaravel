<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\API\QuestionController;
use App\Http\Controllers\API\AnswerController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\API\MeetingController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\API\MeetingCommentController;
use App\Http\Controllers\ProjectAttachmentController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Tutaj umieszczone są trasy API chronione autoryzacją Sanctum.
|
*/


// ✅ Pobranie zalogowanego użytkownika
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user(), 200, ['Accept' => 'application/json']);
});

// ✅ Dashboard API
Route::middleware(['auth:sanctum'])->get('/dashboard', [DashboardController::class, 'index'])
    ->name('api.dashboard');

// ✅ Trasy dla projektowych załączników
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('project-attachments', ProjectAttachmentController::class);
});

// ✅ Trasa dla komentarzy pod spotkaniami
Route::middleware('auth:sanctum')->post('meeting-comments', [MeetingCommentController::class, 'store']);

// ✅ Trasy autoryzacji (logowanie i rejestracja) – publiczne
Route::middleware('api')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// ✅ Trasy chronione autoryzacją Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // 🔹 Zarządzanie kontem użytkownika
    Route::post('logout', [AuthController::class, 'logout']);
    
    // 🔹 Projekty – pełen CRUD (API)
    Route::apiResource('projects', ProjectController::class)->names('api.projects');

    // 🔹 Pytania i odpowiedzi
    Route::apiResource('questions', QuestionController::class);
    Route::apiResource('answers', AnswerController::class)->only(['store', 'destroy']);

    // 🔹 Załączniki powiązane z pytaniami/odpowiedziami
    Route::apiResource('attachments', AttachmentController::class)->only(['store', 'destroy']);

    

    // 🔹 Trasy dla uczestników spotkania
    Route::post('meetings/{meeting}/participants', [MeetingController::class, 'addParticipant']);
    Route::get('meetings/{meeting}/participants', [MeetingController::class, 'participants']);

    // 🔹 Pobieranie spotkań dla danego projektu (DODANE `auth:sanctum` dla autoryzacji)
    Route::middleware('auth:sanctum')->get('projects/{project}/meetings', [MeetingController::class, 'index']);

    // 🔹 Pliki
    Route::apiResource('files', FileController::class)->only(['store', 'destroy']);
});
